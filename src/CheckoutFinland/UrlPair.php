<?php
namespace CheckoutFinland;

class RedirectUrl
{
    public function expose(): array
    {
        return get_object_vars($this);
    }
}
