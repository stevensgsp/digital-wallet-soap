<?php

namespace App\Http\SoapServices\Types;

use App\Http\SoapServices\Types\ClientType;

class ResponseType
{
    /**
     * @var bool
     */
    public $success;

    /**
     * @var int
     */
    public $cod_error;

    /**
     * @var string
     */
    public $message_error;

    /**
     * Client type constructor.
     *
     * @param  array  $data
     */
    public function __construct(array $data = [])
    {
        $this->success = $data['success'] ?? true;
        $this->cod_error = $data['cod_error'] ?? 0;
        $this->message_error = $data['message_error'] ?? '';
    }
}
