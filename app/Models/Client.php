<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'identification',
        'firstname',
        'lastname',
        'email',
        'phone',
    ];

    /**
     * Returns the wallet of the client.
     *
     * @return \App\Models\Wallet
     */
    public function getWallet(): Wallet
    {
        if (empty($this->wallet)) {
            return $this->wallet()->create(['balance' => 0]);
        }

        return $this->wallet;
    }

    /**
     * @return \Jenssegers\Mongodb\Relations\HasOne
     */
    public function wallet()
    {
        return $this->hasOne(\App\Models\Wallet::class);
    }

    /**
     * @return \Jenssegers\Mongodb\Relations\HasMany
     */
    public function sessions()
    {
        return $this->hasMany(\App\Models\Session::class);
    }
}
