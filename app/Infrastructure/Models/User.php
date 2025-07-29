<?php

namespace App\Infrastructure\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Infrastructure\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'email_verified_at',
        'password',
        'avatar',
        'rating',
        'role',
        'last_login_at',
        'is_active',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'rating' => 'integer',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    protected $attributes = [
        'role' => 'user',
        'is_active' => true,
        'rating' => 0,
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function isAdmin(): bool
    {
        return ($this->role ?? 'user') === 'admin';
    }

    public function isActive(): bool
    {
        return $this->is_active ?? true;
    }

    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    public function sendEmailVerificationNotification()
    {
        // $this->notify(new QueuedVerifyEmail()); // TODO: Queue bilan ishlatish uchun. queue:work ishga tushirilishi kerak.
        $this->notify(new VerifyEmail());
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
}
