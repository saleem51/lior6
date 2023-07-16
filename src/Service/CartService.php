<?php

namespace App\Service;

use App\Entity\Cart\CartItem;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{

    public function __construct(protected RequestStack $requestStack, protected ProductRepository $productRepository){}


    protected function getCart(): array
    {
        return $this->requestStack->getSession()->get('cart', []);
    }


    protected function saveCart(array $cart)
    {
        $this->requestStack->getSession()->set('cart', $cart);
    }

    public function add (int $id)
    {
        $cart = $this->getCart();

        if(!array_key_exists($id, $cart)){
            $cart[$id] = 0 ;
        }

        $cart[$id]++;

        $this->saveCart($cart);

    }

    public function getTotal(): int
    {
        $total = 0;

        foreach ($this->getCart() as $id => $qty){

            $product = $this->productRepository->find($id);

            if(!$product) {
                continue;
            }

            $total = $product->getPrice() * $qty;

        }

        return $total;
    }

    public function getDetailedCartItems(): array
    {

        $detailedCart = [];


        foreach ($this->getCart() as $id => $qty){

            $product = $this->productRepository->find($id);

            if(!$product) {
                continue;
            }
            $detailedCart[] = new CartItem($product, $qty);

        }

        return $detailedCart;

    }

    public function remove(int $id)
    {
        $cart = $this->getCart();

        unset($cart[$id]);

        $this->saveCart($cart);
    }

    public function decrement(int $id)
    {
        $cart = $this->getCart();

        if(!array_key_exists($id,$cart)){
            return;
        }

        if($cart[$id] === 1){
           $this->remove($id);
           return;
        }

        $cart[$id]--;

        $this->saveCart($cart);
    }
}