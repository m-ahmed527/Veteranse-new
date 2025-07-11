<?php

namespace App\Models;


use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'otp',
        'otp_verified_at',
        'otp_expires_at',
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
            'vendor_store_gallery' => 'json',

        ];
    }
    // public function toArray()
    // {
    //     $array = parent::toArray();

    //     if ($this->role === 'user') {
    //         unset(
    //             $array['vendor_store_image'],
    //             $array['vendor_store_gallery'],
    //             $array['vendor_store_title'],
    //             $array['vendor_store_description']
    //         );
    //     }

    //     return $array;
    // }

    public function isVendor(): bool
    {
        return $this->role == 'vendor';
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }



    public function wishlistedProducts(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'wishlistable', 'wishlists')->withTimestamps();
    }

    public function wishlistedServices(): MorphToMany
    {
        return $this->morphedByMany(Service::class, 'wishlistable', 'wishlists')->withTimestamps();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function stripeAccount(): HasOne
    {
        return $this->hasOne(StripeAccount::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Add balance to wallet
     */
    public function creditWallet($amount, $reason = null)
    {
        $this->increment('wallet_balance', $amount);

        $this->walletTransactions()->create([
            'amount' => $amount,
            'type' => 'credit',
            'reason' => $reason,
        ]);
    }

    /**
     * Subtract balance from wallet
     */
    public function debitWallet($amount, $reason = null)
    {
        if ($this->wallet_balance < $amount) {
            throw new \Exception('Insufficient wallet balance');
        }

        $this->decrement('wallet_balance', $amount);

        $this->walletTransactions()->create([
            'amount' => $amount,
            'type' => 'debit',
            'reason' => $reason,
        ]);
    }
}
