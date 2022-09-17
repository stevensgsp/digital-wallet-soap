<?php

namespace App\Http\SoapServices\Types;

class CheckoutResponseType extends ResponseType
{
    /**
     * @var array
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

        $this->data = $data['data'] ?? [];
    }
}
