<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // Pakai properti klasik (bukan atribut #[Fillable]) agar jalan di Laravel 12 maupun 13.
    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone',
        'username', 'bio', 'avatar', 'cover_photo',
        'birth_date', 'nik', 'gender', 'occupation',
        'disability', 'city', 'address', 'postal_code', 'website',
    ];

    protected $hidden = ['password', 'remember_token'];

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

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}