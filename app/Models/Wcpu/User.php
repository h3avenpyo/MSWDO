<?php

namespace App\Models\Wcpu;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The database connection name for WCPU models.
     *
     * @var string
     */
    protected $connection = 'wcpu';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'username',
        'password',
        'phone_number',
        'position',
        'department',
        'barangay',
        'is_active',
        'bcpc_is_active',
        'vawc_is_active',
        'last_login_at',
        'last_login_ip',
        'notifications_last_viewed_at',
        'read_notifications',
        'failed_login_attempts',
        'locked_until',
        'account_locked',
    ];

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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['full_name', 'profile_photo_url'];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'bcpc_is_active' => 'boolean',
        'vawc_is_active' => 'boolean',
        'notifications_last_viewed_at' => 'datetime',
        'read_notifications' => 'array',
    ];

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
    
    /**
     * Get the URL to the user's profile photo.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo_path
                    ? asset('storage/'.$this->profile_photo_path)
                    : $this->defaultProfilePhotoUrl();
    }
    
    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function defaultProfilePhotoUrl()
    {
        return asset('images/mswdo_logo.jpg');
    }
    
    /**
     * Update the user's last login timestamp and IP address.
     *
     * @param string|null $ip
     * @return void
     */
    public function updateLastLogin($ip = null)
    {
        $this->last_login_at = now();
        $this->last_login_ip = $ip ?: request()->ip();
        $this->save();
    }
    
    /**
     * Get the roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id')
            ->withTimestamps();
    }
    
    /**
     * Get the cases assigned to the user.
     */
    public function assignedCases()
    {
        return $this->hasMany(CaseModel::class, 'assigned_to');
    }
    
    /**
     * Get the cases created by the user.
     */
    public function createdCases()
    {
        return $this->hasMany(CaseModel::class, 'created_by');
    }
    
    /**
     * Get the case notes created by the user.
     */
    public function caseNotes()
    {
        return $this->hasMany(CaseNote::class);
    }
    
    /**
     * Check if the user has a specific role.
     *
     * @param  string|array  $roles
     * @return bool
     */
    public function hasRole($roles)
    {
        if (is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }
        
        return (bool) $this->roles->whereIn('name', $roles)->count();
    }
    
    /**
     * Check if the user has any of the given roles.
     *
     * @param  array  $roles
     * @return bool
     */
    public function hasAnyRole($roles)
    {
        return $this->hasRole($roles);
    }
    
    /**
     * Check if the user has all of the given roles.
     *
     * @param  array  $roles
     * @return bool
     */
    public function hasAllRoles($roles)
    {
        $roles = is_array($roles) ? $roles : func_get_args();
        
        return count($roles) === $this->roles->whereIn('name', $roles)->count();
    }

    public function configuredCaseDepartments(): array
    {
        $department = strtolower(trim((string) $this->department));
        $normalized = str_replace(['/', '-', ' ', '_'], '', $department);
        $position = strtolower(trim((string) $this->position));

        // WCPU SILANG is the VAWC administrator account.
        if ($position === 'administrator' && $normalized === 'wcpusilang') {
            return ['VAWC'];
        }

        // Check for explicit multi-department combinations first
        if (str_contains($normalized, 'bcpc') && str_contains($normalized, 'vawc')) {
            return ['BCPC', 'VAWC'];
        }

        // Check for BCPC specifically (but not combined with VAWC)
        if (str_contains($normalized, 'bcpc')) {
            return ['BCPC'];
        }

        // Check for VAWC specifically (but not combined with BCPC)
        if (str_contains($normalized, 'vawc')) {
            return ['VAWC'];
        }

        // If position contains "bcpc" but department doesn't, use position to determine
        if (str_contains($position, 'bcpc')) {
            return ['BCPC'];
        }

        // If position contains "vawc" but department doesn't, use position to determine
        if (str_contains($position, 'vawc')) {
            return ['VAWC'];
        }

        // WCPU and MSWD have access to both departments
        if (str_contains($normalized, 'wcpu') || str_contains($normalized, 'mswd')) {
            return ['BCPC', 'VAWC'];
        }

        // If position contains "wcpu" or "mswd" but department doesn't, use position
        if (str_contains($position, 'wcpu') || str_contains($position, 'mswd')) {
            return ['BCPC', 'VAWC'];
        }

        // If department is empty or doesn't match any known pattern, default to VAWC
        return ['VAWC'];
    }

    public function caseDepartments(): array
    {
        return array_values(array_filter($this->configuredCaseDepartments(), function (string $department) {
            return $department === 'BCPC'
                ? ($this->bcpc_is_active ?? true)
                : ($this->vawc_is_active ?? true);
        }));
    }

    public function isMultiCaseDepartment(): bool
    {
        return count($this->caseDepartments()) > 1;
    }

    public function canAccessCaseDepartment(string $department): bool
    {
        return in_array(strtoupper($department), $this->caseDepartments(), true);
    }

    public function defaultCaseDepartment(): string
    {
        $departments = $this->caseDepartments();
        if (empty($departments)) {
            return $this->configuredCaseDepartments()[0] ?? 'VAWC';
        }
        return $departments[0];
    }

    public function isBcpcAdmin(): bool
    {
        return strtolower(trim((string) $this->position)) === 'administrator'
            && $this->caseDepartments() === ['BCPC'];
    }

    public function isVawcAdmin(): bool
    {
        return strtolower(trim((string) $this->position)) === 'administrator'
            && $this->caseDepartments() === ['VAWC'];
    }

    public function isDepartmentAdmin(): bool
    {
        return $this->isBcpcAdmin() || $this->isVawcAdmin();
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\Wcpu\ResetPasswordNotification($token));
    }
}
