<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'balance',
    ];

    /**
     * Recharge the wallet.
     *
     * @param  float    $amount
     * @return self
     */
    public function recharge(float $amount): self
    {
        $this->balance += $amount;

        $this->save();

        return $this;
    }

    /**
     * Charge the wallet.
     *
     * @param  float    $amount
     * @return self
     */
    public function charge(float $amount)
    {
        $this->balance -= $amount;

        $this->save();

        return $this;
    }

    /**
     * Check if the balance has the given amount.
     *
     * @param  float  $amount
     * @return bool
     */
    public function haveEnoughBalanceFor(float $amount): bool
    {
        return $this->balance >= $amount;
    }
}
