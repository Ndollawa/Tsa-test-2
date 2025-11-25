<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'referred_by',
        'enrolled_date',
    ];

    /** The user who referred this user (nullable) */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(self::class, 'referred_by');
    }

    /** Users referred by this user */
    public function referrals(): HasMany
    {
        return $this->hasMany(self::class, 'referred_by');
    }

    /** All orders placed by this user */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'purchaser_id');
    }

    /**
     * Categories assigned to this user (many-to-many via user_category pivot)
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'user_category',
            'user_id',
            'category_id'
        );
    }

    /**
     * Helper: is this user a distributor?
     */
    public function isDistributor(): bool
    {
        return $this->categories()->where('name', 'Distributor')->exists();
    }
}
