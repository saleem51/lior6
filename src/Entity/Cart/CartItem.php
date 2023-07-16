<?php

namespace App\Entity\Cart;

use App\Entity\Product;

class CartItem
{
    /**
     * @var Product $product
     */
    public $product;
    public $qty;

    public function __construct(Product $product, int $qty){
        $this->product = $product;
        $this->qty = $qty;
    }

    public function getTotal(): int
    {
        return $this->product->getPrice() * $this->qty;
    }


}