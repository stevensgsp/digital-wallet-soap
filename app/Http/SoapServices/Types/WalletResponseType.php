<?php

namespace App\Http\SoapServices\Types;

use App\Http\SoapServices\Types\WalletType;

class WalletResponseType extends ResponseType
{
    /**
     * @var \App\Http\SoapServices\Types\WalletType
     */
    public $data;

    /**
     * Client type constructor.
     *
     * @param  array  $data
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);

        $this->data = $data['data'] ?? new WalletType();
    }
}
