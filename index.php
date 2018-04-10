<?php
require 'vendor/autoload.php';

use CheckoutFinland\Api;
use CheckoutFinland\Item;
use CheckoutFinland\Customer;
use CheckoutFinland\Address;
use CheckoutFinland\UrlPair;

$testMerchantId     = "375917";
$testMerchantSecret = "SAIPPUAKAUPPIAS";

$api = new Api($testMerchantId, $testMerchantSecret);
$api->openPayment();
