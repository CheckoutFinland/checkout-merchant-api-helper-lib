<?php
namespace CheckoutFinland;

class Item
{
    public function expose(): array
    {
        return get_object_vars($this);
    }
}
