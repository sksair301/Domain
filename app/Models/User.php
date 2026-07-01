<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'branch_id',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function manager()
    {
        return $this->hasOne(Manager::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function isSuperAdmin()
    {
        return $this->role === 'superadmin';
    }

    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function isEmployee()
    {
        return $this->role === 'employee';
    }

    public static function getAdminEmails()
    {
        $emails = self::where('role', 'superadmin')->pluck('email')->toArray();
        $envEmails = config('mail.admin_emails', '');
        $hardcoded = $envEmails ? array_map('trim', explode(',', $envEmails)) : [];
        return array_unique(array_merge($emails, $hardcoded));
    }

    public static function getManagerEmailsByBranch($branchId)
    {
        if (!$branchId) return [];
        return self::where('role', 'manager')
                   ->where('branch_id', $branchId)
                   ->pluck('email')
                   ->toArray();
    }

    public static function getEmployeeEmailById($employeeId)
    {
        if (!$employeeId) return [];
        $email = self::where('id', $employeeId)->value('email');
        return $email ? [$email] : [];
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
