<?php

namespace App\Http\SoapServices;

use App\Http\SoapServices\Types\CheckoutResponseType;
use App\Http\SoapServices\Types\ClientType;
use App\Http\SoapServices\Types\RegisterClientResponseType;
use App\Http\SoapServices\Types\WalletResponseType;
use App\Http\SoapServices\Types\WalletType;
use App\Mail\ConfirmCheckout;
use App\Models\Client;
use App\Models\Product;
use App\Models\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use SoapFault;

/**
 * Class that is used as a SOAP gateway to handle the digital wallet.
 */
class WalletService
{
    /**
     * Registers a client, returning status of true, or throws SoapFault.
     *
     * @param  string  $identification
     * @param  string  $firstname
     * @param  string  $lastname
     * @param  string  $email
     * @param  string  $phone
     *
     * @return \App\Http\SoapServices\Types\RegisterClientResponseType
     * @throws SoapFault
     */
    public function registerClient(
        string $identification,
        string $firstname,
        string $lastname,
        string $email,
        string $phone
    ) {
        // validate the data
        validator($data = compact('identification', 'firstname', 'lastname', 'email', 'phone'), [
            'identification' => ['string', 'filled', 'max:100'],
            'firstname' => ['string', 'filled', 'max:100'],
            'lastname' => ['string', 'filled', 'max:100'],
            'email' => ['string', 'filled', 'max:100'],
            'phone' => ['string', 'filled', 'max:100'],
        ])->validate();

        // create the client
        $client = Client::create($data);

        // return data
        return new RegisterClientResponseType([
            'success' => true,
            'cod_error' => 0,
            'data' => new ClientType($client->toArray()),
        ]);
    }

    /**
     * Recharge the wallet of the specified client, returning status of true, or throws SoapFault.
     *
     * @param  string  $identification
     * @param  string  $phone
     * @param  float   $amount
     *
     * @return \App\Http\SoapServices\Types\WalletResponseType
     * @throws SoapFault
     * @throws ValidationException
     */
    public function rechargeWallet(string $identification, string $phone, float $amount)
    {
        // validate the data
        validator($data = compact('identification', 'phone', 'amount'), [
            'identification' => ['string', 'filled', 'max:100'],
            'phone' => ['string', 'filled', 'max:100'],
            'amount' => ['numeric', 'filled', 'min:0.01', 'max:100000000.00'],
        ])->validate();

        // get the client
        $client = Client::where('identification', $data['identification'])->where('phone', $data['phone'])->firstOrFail();

        // recharge the wallet
        $wallet = $client->getWallet()->recharge($data['amount']);

        // return data
        return new WalletResponseType([
            'success' => true,
            'cod_error' => 0,
            'data' => new WalletType($wallet->toArray()),
        ]);
    }

    /**
     * Check balance of the wallet of the specified client, returning status of true, or throws SoapFault.
     *
     * @param  string  $identification
     * @param  string  $phone
     *
     * @return \App\Http\SoapServices\Types\WalletResponseType
     * @throws SoapFault
     * @throws ValidationException
     */
    public function checkBalance(string $identification, string $phone)
    {
        // validate the data
        validator($data = compact('identification', 'phone'), [
            'identification' => ['string', 'filled', 'max:100'],
            'phone' => ['string', 'filled', 'max:100'],
        ])->validate();

        // get the client
        $client = Client::where('identification', $data['identification'])->where('phone', $data['phone'])->firstOrFail();

        // recharge the wallet
        $wallet = $client->getWallet();

        // return data
        return new WalletResponseType([
            'success' => true,
            'cod_error' => 0,
            'data' => new WalletType($wallet->toArray()),
        ]);
    }

    /**
     * Checkout process, returning status of true, or throws SoapFault.
     *
     * @param  string  $identification
     * @param  string  $phone
     *
     * @return \App\Http\SoapServices\Types\CheckoutResponseType
     * @throws SoapFault
     * @throws ValidationException
     */
    public function checkout(string $identification, string $phone, string $productCode)
    {
        // validate the data
        validator($data = compact('identification', 'phone', 'productCode'), [
            'identification' => ['string', 'filled', 'max:100'],
            'phone' => ['string', 'filled', 'max:100'],
            'productCode' => ['string', 'filled', 'max:100'],
        ])->validate();

        // get the client
        $client = Client::where('identification', $data['identification'])->where('phone', $data['phone'])->firstOrFail();

        // get the product
        $product = Product::where('code', $data['productCode'])->firstOrFail();

        // check balance
        if (! $client->getWallet()->haveEnoughBalanceFor($product->price)) {
            throw new SoapFault('SOAP-ENV:Client', 'Not enough balance.');
        }

        // create the session
        $session = tap(new Session(), function ($session) use ($product, $client) {
            $session->token = (string) \Str::uuid();
            $session->product_id = $product->id;
            $session->product_price = $product->price;
            $session->save();

            $client->sessions()->save($session);
        });


        // send token via mail
        Mail::to($client->email)->send(new ConfirmCheckout($session->token));

        // return data
        return new CheckoutResponseType([
            'success' => true,
            'cod_error' => 0,
            'data' => [
                'sessionId' => $session->id,
                'message' => 'Token sent to email.',
            ],
        ]);
    }

    /**
     * Confirm the cehckout process, returning status of true, or throws SoapFault.
     *
     * @param  string  $sessionId
     * @param  string  $token
     *
     * @return \App\Http\SoapServices\Types\WalletResponseType
     * @throws SoapFault
     * @throws ValidationException
     */
    public function confirmCheckout(string $sessionId, string $token)
    {
        // validate the data
        validator($data = compact('sessionId', 'token'), [
            'sessionId' => ['string', 'filled', 'max:100'],
            'token' => ['string', 'filled', 'max:100'],
        ])->validate();

        // get the session
        $session = Session::findOrFail($data['sessionId']);

        // validate token
        if ($session->isExpired()) {
            throw new SoapFault('SOAP-ENV:Client', 'Session expired.');
        }

        // validate token
        if ($session->token !== $data['token']) {
            throw new SoapFault('SOAP-ENV:Client', 'Token not valid.');
        }

        // get the client
        $client = $session->client;

        // check balance
        if (! $client->getWallet()->haveEnoughBalanceFor($session->product_price)) {
            throw new SoapFault('SOAP-ENV:Client', 'Not enough balance.');
        }

        // execute checkout
        $client->getWallet()->charge($session->product_price);

        // expire the session
        $session->expire();

        // return data
        return new WalletResponseType([
            'success' => true,
            'cod_error' => 0,
            'data' => new WalletType($client->getWallet()->toArray()),
        ]);
    }
}
