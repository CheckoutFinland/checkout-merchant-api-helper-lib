<?php
namespace CheckoutFinland;

class Comission
{
    private $merchant;
    private $amount;

    public function __construct(
        int $merchant,
        int $amount
    ) {
        $this->merchant = $merchant;
        $this->amount = $amount;
    }

    public function expose()
    {
        return get_object_vars($this);
    }
}
