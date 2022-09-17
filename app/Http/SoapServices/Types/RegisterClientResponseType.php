<?php

namespace App\Http\SoapServices\Types;

use App\Http\SoapServices\Types\ClientType;

class RegisterClientResponseType extends ResponseType
{
    /**
     * @var \App\Http\SoapServices\Types\ClientType
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

        $this->data = $data['data'] ?? new ClientType();
    }
}
