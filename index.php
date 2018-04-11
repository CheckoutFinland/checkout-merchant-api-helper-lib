<?php
require 'vendor/autoload.php';

use CheckoutFinland\Api;
use CheckoutFinland\Item;
use CheckoutFinland\Customer;
use CheckoutFinland\Address;
use CheckoutFinland\UrlPair;

$testMerchantId     = "3";
$testMerchantSecret = "kissa123";

$api = new Api($testMerchantId, $testMerchantSecret, "http://localhost:4001");
