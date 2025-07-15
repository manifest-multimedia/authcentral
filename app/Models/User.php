<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'student_id',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'guardian_name',
        'guardian_phone',
        'course',
        'level',
        'department',
        'enrollment_date',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'role_names',
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
            'date_of_birth' => 'date',
            'enrollment_date' => 'date',
        ];
    }
    
    /**
     * Get role names for the user.
     *
     * @return array
     */
    public function getRoleNamesAttribute()
    {
        return $this->roles->pluck('name')->toArray();
    }
    
    /**
     * Check if the user has System level access.
     *
     * @return bool
     */
    public function isSystem(): bool
    {
        return $this->hasRole('System');
    }
    
    /**
     * Check if the user has Super Admin level access.
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }
    
    /**
     * Check if the user has Administrator level access.
     *
     * @return bool
     */
    public function isAdministrator(): bool
    {
        return $this->hasRole('Administrator');
    }
    
    /**
     * Check if the user is a Student.
     *
     * @return bool
     */
    public function isStudent(): bool
    {
        return $this->hasRole('Student');
    }
}
