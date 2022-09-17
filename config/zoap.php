<?php

use App\Http\SoapServices\Types\ClientType;
use App\Http\SoapServices\Types\WalletResponseType;
use App\Http\SoapServices\Types\ResponseType;
use App\Http\SoapServices\Types\WalletType;
use App\Http\SoapServices\WalletService;
use Viewflex\Zoap\Demo\Types\KeyValue;

return [

    // Service configurations.

    'services'          => [

        'demo'              => [
            'name'              => 'Demo',
            'class'             => 'Viewflex\Zoap\Demo\DemoService',
            'exceptions'        => [
                'Exception'
            ],
            'types'             => [
                'keyValue'          => 'Viewflex\Zoap\Demo\Types\KeyValue',
                'product'           => 'Viewflex\Zoap\Demo\Types\Product'
            ],
            'strategy'          => 'ArrayOfTypeComplex',
            'headers'           => [
                'Cache-Control'     => 'no-cache, no-store'
            ],
            'options'           => []
        ],

        'wallet'              => [
            'name'              => 'Wallet',
            'class'             => WalletService::class,
            'exceptions'        => [
                'Exception',
                'Illuminate\Validation\ValidationException',
                'Illuminate\Database\Eloquent\ModelNotFoundException',
            ],
            'types'             => [
                'keyValue'          => KeyValue::class,
                'client'            => ClientType::class,
                'response'          => ResponseType::class,
                'recharge-wallet'   => WalletResponseType::class,
                'wallet'            => WalletType::class,
            ],
            'strategy'          => 'ArrayOfTypeComplex',
            'headers'           => [
                'Cache-Control'     => 'no-cache, no-store'
            ],
            'options'           => []
        ],

    ],


    // Log exception trace stack?

    'logging'       => true,


    // Mock credentials for demo.

    'mock'          => [
        'user'              => 'test@test.com',
        'password'          => 'tester',
        'token'             => 'tGSGYv8al1Ce6Rui8oa4Kjo8ADhYvR9x8KFZOeEGWgU1iscF7N2tUnI3t9bX'
    ],


];
