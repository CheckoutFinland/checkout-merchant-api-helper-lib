<?php
namespace CheckoutFinland;

class Customer
{
    public function expose(): array
    {
        return get_object_vars($this);
    }
}
