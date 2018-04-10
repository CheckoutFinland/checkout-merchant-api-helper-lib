<?php
namespace CheckoutFinland;

use CheckoutFinland\Item;
use CheckoutFinland\Customer;
use CheckoutFinland\Address;
use CheckoutFinland\RedirectUrl;

class Api
{
    public function __construct(string $merchantId, string $merchantSecret)
    {
        $this->merchantId = $merchantId;
        $this->merchantSecret = $merchantSecret;
    }

    public function openPayment(
        string $hmacAlgorithm = "sha256",
        string $httpMethod = "post",
        array $items = [],
        Customer $customer = null,
        Address $deliveryAddress = null,
        Address $invoicingAddress = null,
        RedirectUrl $success
    ) {
        echo("openPayment");
    }
}
