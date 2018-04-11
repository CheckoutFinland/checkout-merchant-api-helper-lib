<?php
namespace CheckoutFinland;

use CheckoutFinland\Item;
use CheckoutFinland\Customer;
use CheckoutFinland\Address;
use CheckoutFinland\UrlPair;

class Api
{
    private $merchantId;
    private $merchantSecret;
    private $serverUrl;

    public function __construct(
        string $merchantId,
        string $merchantSecret,
        string $serverUrl = "https://api.checkout.fi"
    ) {
        $this->merchantId = $merchantId;
        $this->merchantSecret = $merchantSecret;
        $this->serverUrl = $serverUrl;
    }

    public function openPayment(
        string $hmacAlgorithm = "sha256",
        string $httpMethod = "post",
        array $items = [],
        Customer $customer = null,
        Address $deliveryAddress = null,
        Address $invoicingAddress = null,
        UrlPair $redirectUrls = null,
        UrlPair $callbackUrls = null
    ) {
        // assert $items is an array of items?
        Api::arrayAll(function ($item) {
            return get_class($item) == "Item";
        }, $items);

        // make sure all parameter vars contain an appropriate object
        $customer = $customer ?? new Customer();
        $deliveryAddress = $deliveryAddress ?? new Address();
        $invoicingAddress = $invoicingAddress ?? new Address();
        $redirectUrls = $redirectUrls ?? new UrlPair();
        $callbackUrls = $callbackUrls ?? new UrlPair();

        // map items into an array of all member variables
        $items = array_map(function ($item) {
            return $item->expose();
        }, $items);

        $body = array(
            "stamp" => 0,
            "reference" => 0,
            "amount" => 0,
            "language" => "",
            "items" => $items,
            "customer" => $customer->expose(),
            "deliveryAddress" => $deliveryAddress->expose(),
            "invoicingAddress" => $invoicingAddress->expose(),
            "redirectUrls" => $redirectUrls->expose(),
            "callbackUrls" => $callbackUrls->expose()
        );
        $body = json_encode($body);

        $headers = array(
            'checkout-account' => $this->merchantId,
            'checkout-algorithm' => $hmacAlgorithm,
            'checkout-method' => 'POST'
        );

        // HTTP request
        $response = \Httpful\Request::post($this->serverUrl)
            ->sendsJson()
            ->addHeaders(array_merge(
                $headers,
                array(
                    'signature' => Api::calculateHMAC(
                        $headers,
                        $body,
                        $this->merchantSecret,
                        $hmacAlgorithm
                    )
                )
            ))
            ->body($body)
            ->send();
    }

    private static function calculateHmac(
        array $headers,
        string $body,
        string $secretKey,
        string $hmacAlgorithm
    ): string {
        $payload = array_map(function ($k, $v) {
            return $k . ":" . $v;
        }, array_keys($headers), $headers);
        array_push($payload, $body);

        return hash_hmac($hmacAlgorithm, join('\n', $payload), $secretKey);
    }

    private static function arrayAll($func, $array): bool
    {
        $conformes = array_map($func, $array);
        $result = array_filter($conformes, function ($item) {
            return $item;
        });
        return true;
    }
}
