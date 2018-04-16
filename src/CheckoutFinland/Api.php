<?php
namespace CheckoutFinland;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

use CheckoutFinland\Item;
use CheckoutFinland\Customer;
use CheckoutFinland\Address;
use CheckoutFinland\UrlPair;

class Api
{
    private $merchantId;
    private $merchantSecret;
    private $serverUrl;

    private const DEFAULT_PAYMENT_OPTS = array(
        'stamp' => '',
        'hmacAlgorithm' => 'sha256',
        'httpMethod' => 'post',
        'items' => [],
        'customer' => null,
        'deliveryAddress' => null,
        'invoicingAddress' => null,
        'redirectUrls' => null,
        'callbackUrls' => null
    );

    private const MANDATORY_PAYMENT_FIELDS = array(
        'stamp',
        'reference',
        'amount',
        'language',
        'items',
        'customer',
        'deliveryAddress',
        'invoicingAddress',
        'redirectUrls',
        'callbackUrls'
    );

    public function __construct(
        string $merchantId,
        string $merchantSecret,
        string $serverUrl = 'https://api.checkout.fi'
    ) {
        $this->merchantId = $merchantId;
        $this->merchantSecret = $merchantSecret;
        $this->serverUrl = $serverUrl;
    }

    public function openPayment(
        string   $reference,
        int      $amount,
        string   $currency,
        string   $language,
        array    $opts = []
    ): string {
        $opts = array_merge(
            API::DEFAULT_PAYMENT_OPTS,
            // default stamp to uuidv4, has to be generated outside of the const declaration
            array('stamp' => Uuid::uuid4()),
            $opts
        );

        // assert $items is an array of items?
        Api::arrayAll(function ($item) {
            return get_class($item) == 'Item';
        }, $opts['items']);

        // make sure all parameter vars contain an appropriate object
        $opts['customer'] = $opts['customer'] ?? new Customer();
        $opts['deliveryAddress'] = $opts['deliveryAddress'] ?? new Address();
        $opts['invoicingAddress'] = $opts['invoicingAddress'] ?? new Address();
        $opts['redirectUrls'] = $opts['redirectUrls'] ?? new UrlPair();
        $opts['callbackUrls'] = $opts['callbackUrls'] ?? new UrlPair();

        $body = array_merge(
            // pick mandatory fields from $opts and map into array for easy passing to json_decode
            Api::exposeMandatoryFields($opts),
            // merge with the fields not passed through $opts
            array(
                'reference' => $reference,
                'amount' => $amount,
                'currency' => $currency,
                'language' => $language,
            )
        );
        $body = json_encode($body);

        $headers = array(
            'checkout-account' => $this->merchantId,
            'checkout-algorithm' => $opts['hmacAlgorithm'],
            'checkout-method' => 'POST'
        );

        // HTTP request
        $response = \Httpful\Request::post($this->serverUrl . '/payments')
            ->sendsJson()
            ->addHeaders(array_merge(
                $headers,
                // The signature is calculated and added to the other headers
                array(
                    'signature' => Api::calculateHMAC(
                        $headers,
                        $body,
                        $this->merchantSecret,
                        $opts['hmacAlgorithm']
                    )
                )
            ))
            ->body($body)
            ->send();

        return $response;
    }

    private static function exposeMandatoryFields($opts): array
    {
        return array_map(function ($field) {
            if (method_exists($field, 'expose')) {
                return $field->expose();
            } elseif (gettype($field) == 'array') {
                return array_map(function ($i) {
                    $i->expose();
                }, $field);
            }
            return $field;
        }, Api::arrayPick(Api::MANDATORY_PAYMENT_FIELDS, $opts));
    }

    private static function calculateHmac(
        array $headers,
        string $body,
        string $secretKey,
        string $hmacAlgorithm
    ): string {
        $payload = array_map(function ($k, $v) {
            return $k . ':' . $v;
        }, array_keys($headers), $headers);
        array_push($payload, $body);

        return hash_hmac($hmacAlgorithm, join("\n", $payload), $secretKey);
    }

    private static function arrayAll($func, $array): bool
    {
        return array_reduce($array, function ($carry, $item) use ($func) {
            return $carry ? $func($item) : false;
        }, true);
    }

    private static function arrayPick(array $keys, array $items): array
    {
        return array_filter($items, function ($v, $k) use ($keys) {
            return in_array($k, $keys);
        }, ARRAY_FILTER_USE_BOTH);
    }
}
