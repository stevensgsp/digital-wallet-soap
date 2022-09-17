<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'token',
        'product_id',
        'product_price',
        'expired_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'expired_at' => 'datetime',
    ];

    /**
     * Checks if the session is already expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return ! empty($this->expired_at);
    }

    public function expire(): void
    {
        $this->expired_at = now();

        $this->save();
    }

    /**
     * @return \Jenssegers\Mongodb\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(\App\Models\Client::class);
    }
}
