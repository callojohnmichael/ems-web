<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * IMPORTANT FOR SPATIE
     */
    protected string $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
        'skip_2fa',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'skip_2fa' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isUser(): bool
    {
        return $this->hasRole('user');
    }

    public function isMultimediaStaff(): bool
    {
        return $this->hasRole('multimedia_staff');
    }

    public function dashboardRoute(): string
    {
        if ($this->isAdmin()) {
            return 'admin.dashboard';
        }

        if ($this->isMultimediaStaff()) {
            return 'media.dashboard';
        }

        return 'user.dashboard';
    }
}
