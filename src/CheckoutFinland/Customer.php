<?php
namespace CheckoutFinland;

class Customer
{
    private $email;
    private $firstName;
    private $lastName;
    private $phone;
    private $vatId;

    public function __construct(
        string $email,
        string $firstName,
        string $lastName,
        string $phone,
        string $vatId
    ) {
        $this->email;
        $this->firstName;
        $this->lastName;
        $this->phone;
        $this->vatId;
    }

    public function expose(): array
    {
        return get_object_vars($this);
    }
}
