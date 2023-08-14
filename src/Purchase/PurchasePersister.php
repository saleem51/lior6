<?php

namespace App\Purchase;

use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class PurchasePersister
{
public function __construct(protected  Security $security, protected CartService $cartService, protected EntityManagerInterface $manager){}

    public function storePurchase(Purchase $purchase)
    {
        $user = $this->security->getUser();
        $purchase->setUser($user)
                   ->setPurchasedAt(new \DateTime())
                   ->setTotal($this->cartService->getTotal());

        $this->manager->persist($purchase);

        foreach ($this->cartService->getDetailedCartItems() as $cartItem){
        $purchaseItem = new PurchaseItem();
        $purchaseItem->setPurchase($purchase)
        ->setProduct($cartItem->product)
        ->setProductName($cartItem->product->getName())
        ->setQuantity($cartItem->qty)
        ->setTotal($cartItem->getTotal())
        ->setProductPrice($cartItem->product->getPrice());

        $this->manager->persist($purchaseItem);
        }

        $this->manager->flush();
    }

}