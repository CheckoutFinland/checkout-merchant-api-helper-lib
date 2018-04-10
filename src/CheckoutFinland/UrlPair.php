<?php
namespace CheckoutFinland;

class RedirectUrl
{
    private $success;
    private $cancel;

    public function __construct(
        string $success,
        string $cancel
    ) {
        $this->success;
        $this->cancel;
    }

    public function expose(): array
    {
        return get_object_vars($this);
    }
}
