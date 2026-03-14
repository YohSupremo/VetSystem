<?php

namespace App\Models;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'first_name',
        'last_name',
        'contact_number',
        'address',
        'profile_picture',
        'is_active',
        'email_verified',
        'phone_verified',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified' => 'boolean',
        'last_login' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isDemoAccount(): bool
    {
        $username = strtolower((string) $this->username);
        $email = strtolower((string) $this->email);

        return str_contains($username, 'demo') || str_contains($email, 'demo');
    }

    public function shouldBypassEmailVerification(): bool
    {
        return (bool) config('app.demo_skip_email_verification', false) && $this->isDemoAccount();
    }

    public function hasVerifiedEmail(): bool
    {
        if ($this->shouldBypassEmailVerification()) {
            return true;
        }

        return (bool) $this->email_verified;
    }

    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified' => true,
        ])->save();
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail());
    }

    public function getEmailForVerification(): string
    {
        return (string) $this->email;
    }

    public function petOwner()
    {
        return $this->hasOne(PetOwner::class);
    }

    public function performedSurgeries()
    {
        return $this->hasMany(Surgery::class, 'surgeon_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'veterinarian_id');
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'veterinarian_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function notificationSettings()
    {
        return $this->hasOne(NotificationSetting::class);
    }

    public function staffSchedules()
    {
        return $this->hasMany(StaffSchedule::class);
    }

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function scopeVeterinarians($query)
    {
        return $query->where('role', 'veterinarian');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isVeterinarian()
    {
        return $this->role === 'veterinarian';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPetOwner()
    {
        return $this->role === 'registered_user';
    }
}
