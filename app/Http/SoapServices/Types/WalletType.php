<?php

namespace App\Http\SoapServices\Types;

class WalletType
{
    /**
     * @var float
     */
    public $balance;

    /**
     * Wallet type constructor.
     *
     * @param  array  $data
     */
    public function __construct(array $data = [])
    {
        $this->balance = $data['balance'] ?? '';
    }
}
