<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->decimal('total_amount', 15, 2)->default(0)->after('expiry_date');
            $table->foreignId('sales_person_id')->nullable()->constrained('users')->onDelete('set null')->after('total_amount');
            $table->string('system_status')->nullable()->after('sales_person_id');
            $table->string('manual_status')->nullable()->after('system_status');
            $table->timestamp('last_contacted_at')->nullable()->after('manual_status');
            $table->timestamp('next_followup_at')->nullable()->after('last_contacted_at');
            $table->date('renewal_date')->nullable()->after('next_followup_at');
            $table->foreignId('renewed_by')->nullable()->constrained('users')->onDelete('set null')->after('renewal_date');
        });

        // Migrate data if table has content
        if (Schema::hasColumn('domains', 'status_id')) {
            $domains = Illuminate\Support\Facades\DB::table('domains')->get();
            foreach ($domains as $domain) {
                if ($domain->status_id) {
                    $status = Illuminate\Support\Facades\DB::table('statuses')->where('id', $domain->status_id)->first();
                    Illuminate\Support\Facades\DB::table('domains')->where('id', $domain->id)->update([
                        'system_status' => $status ? $status->name : 'Active',
                    ]);
                }
            }
        }

        Schema::table('domains', function (Blueprint $table) {
            if (Schema::hasColumn('domains', 'sales_person_name')) {
                $table->dropColumn('sales_person_name');
            }
            if (Schema::hasColumn('domains', 'status_id')) {
                $table->dropForeign(['status_id']);
                $table->dropColumn('status_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->string('sales_person_name')->nullable()->after('expiry_date');
            $table->foreignId('status_id')->nullable()->constrained('statuses')->onDelete('set null')->after('sales_person_name');
            
            $table->dropForeign(['renewed_by']);
            $table->dropColumn(['renewed_by', 'renewal_date', 'next_followup_at', 'last_contacted_at', 'manual_status', 'system_status', 'sales_person_id', 'total_amount']);
        });
    }
};
