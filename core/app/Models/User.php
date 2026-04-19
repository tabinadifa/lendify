<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    // Hanya panggil sekali - hapus yang duplikat
    use HasFactory, Notifiable, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'last_active_at',
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
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Gunakan boot() bukan booted() untuk kontrol lebih baik
        static::creating(function ($user) {
            $user->last_active_at = now();
        });

        // Hanya update last_active_at untuk perubahan signifikan
        static::updating(function ($user) {
            // Cek apakah ada perubahan signifikan selain remember_token/last_active_at
            $significantChanges = array_diff_key(
                $user->getDirty(), 
                array_flip(['remember_token', 'last_active_at', 'updated_at'])
            );
            
            if (!empty($significantChanges)) {
                $user->last_active_at = now();
            }
        });
    }

    /**
     * Update last active timestamp tanpa trigger event (untuk login/logout)
     */
    public function updateLastActiveWithoutLogging()
    {
        // Update tanpa memicu event updating
        static::withoutEvents(function () {
            $this->timestamps = false; // Temporarily disable timestamps
            $this->update(['last_active_at' => now()]);
            $this->timestamps = true;
        });
    }

    /**
     * Cek apakah user sedang online (dalam 5 menit terakhir)
     */
    public function isOnline()
    {
        return $this->last_active_at && $this->last_active_at->diffInMinutes(now()) <= 5;
    }
}