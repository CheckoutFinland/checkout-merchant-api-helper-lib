<?php
namespace CheckoutFinland;

use CheckoutFinland\Comission;

class Item
{
    private $unitPrice;
    private $units;
    private $vatPercentage;
    private $productCode;
    private $deliveryDate;
    private $description;
    private $category;
    private $merchant;
    private $stamp;
    private $reference;
    private $comission;

    public function __construct(
        int $unitPrice,
        int $units,
        int $vatPercentage,
        string $productCode,
        string $deliveryDate,
        string $description,
        string $category,
        int $merchant,
        int $stamp,
        int $reference,
        Comission $comission
    ) {
        $this->unitPrice = $unitPrice;
        $this->units = $units;
        $this->vatPercentage = $vatPercentage;
        $this->productCode = $productCode;
        $this->deliveryDate = $deliveryDate;
        $this->description = $description;
        $this->category = $category;
        $this->merchant = $merchant;
        $this->stamp = $stamp;
        $this->reference = $reference;
        $this->comission = $comission ?? new stdClass();
    }

    public function expose(): array
    {
        $comissionData = $this->comission ?
            $this->comission->expose() :
            new stdClass;

        return array_replace(
            get_object_vars($this),
            array("comission" => $comissionData)
        );
    }
}
