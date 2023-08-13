<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Entity\User;
use App\Form\CartConfirmationType;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class PurchaseConfirmationController extends AbstractController
{

    public function __construct(
        protected FormFactoryInterface $formFactory,
        protected RouterInterface $router,
        protected Security $security,
        protected CartService $cartService,
        protected EntityManagerInterface $manager,
    ){}

    #[Route("/purchase/confirm", name: 'purchase_confirm')]
    public function confirm(Request $request)
    {
        $form = $this->formFactory->create(CartConfirmationType::class);

        $form->handleRequest($request);

        if(!$form->isSubmitted()){

            $this->addFlash('warning', 'Vous devez remplir le formulaire de confirmation');
            return new RedirectResponse($this->router->generate('cart_show'));
        }


        /**
         * @var User $user
         */
        $user = $this->security->getUser();

        if(!$user) {
            throw new AccessDeniedException("Vous devez être connecté pour confirmer une commande");
        }

        $cartItems = $this->cartService->getDetailedCartItems();

        if(count($cartItems) === 0) {

            $this->addFlash('Warning', 'Vous ne pouvez confirmer une commande avec un panier vide');

            return new RedirectResponse($this->router->generate("cart_show"));
        }

        /**
         * @var Purchase $purchase
         */
        $purchase = $form->getData();

        $purchase->setUser($user)
                 ->setPurchasedAt(new \DateTime());

        $this->manager->persist($purchase);

        $total = 0;

        foreach ($this->cartService->getDetailedCartItems() as $cartItem){
            $purchaseItem = new PurchaseItem();
            $purchaseItem->setPurchase($purchase)
                         ->setProduct($cartItem->product)
                         ->setProductName($cartItem->product->getName())
                         ->setQuantity($cartItem->qty)
                         ->setTotal($cartItem->getTotal())
                         ->setProductPrice($cartItem->product->getPrice());

            $total += $cartItem->getTotal();

            $this->manager->persist($purchaseItem);
        }

        $purchase->setTotal($total);

        $this->manager->flush();

        $this->addFlash('success', "La commande a bien été enregistrée");
        return new RedirectResponse($this->router->generate('purchase_index'));
    }
}