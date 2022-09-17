<?php

namespace App\Http\SoapServices\Types;

class ClientType
{
    /**
     * @var string
     */
    public $identification;

    /**
     * @var string
     */
    public $firstname;

    /**
     * @var string
     */
    public $lastname;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $phone;

    /**
     * Client type constructor.
     *
     * @param  array  $data
     */
    public function __construct(array $data = [])
    {
        $this->identification = $data['identification'] ?? '';
        $this->firstname = $data['firstname'] ?? '';
        $this->lastname = $data['lastname'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->phone = $data['phone'] ?? '';
    }
}
