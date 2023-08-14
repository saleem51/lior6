<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Entity\User;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class PurchaseConfirmationController extends AbstractController
{

    public function __construct(
        protected CartService $cartService,
        protected EntityManagerInterface $manager,
        protected PurchasePersister $persister,
    ){}

    #[Route("/purchase/confirm", name: 'purchase_confirm')]
    #[isGranted("ROLE_USER", message: "Vous devez être connecté pour confirmer une commande")]
    public function confirm(Request $request)
    {
        $form = $this->createForm(CartConfirmationType::class);

        $form->handleRequest($request);

        if(!$form->isSubmitted()){

            $this->addFlash('warning', 'Vous devez remplir le formulaire de confirmation');
            return $this->redirectToRoute('cart_show');
        }


        /**
         * @var User $user
         */
        $user = $this->getUser();

        $cartItems = $this->cartService->getDetailedCartItems();

        if(count($cartItems) === 0) {

            $this->addFlash('Warning', 'Vous ne pouvez confirmer une commande avec un panier vide');

            return $this->redirectToRoute("cart_show");
        }

        /**
         * @var Purchase $purchase
         */
        $purchase = $form->getData();

        $this->persister->storePurchase($purchase);

        $this->cartService->empty();

        $this->addFlash('success', "La commande a bien été enregistrée");
        return $this->redirectToRoute('purchase_index');
    }
}