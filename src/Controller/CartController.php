<?php

namespace App\Controller;

use App\Form\CartConfirmationType;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{

    public function __construct(protected ProductRepository $productRepository, protected CartService $cartService){}


    #[Route('/cart/add/{id}', name: 'app_cart', requirements: ['id' => '\d+'])]
    public function add($id, Request $request): Response
    {
        $product = $this->productRepository->find($id);

        if(!$product){
            throw $this->createNotFoundException("Le produit $id n'existe pas");
        }

        $this->cartService->add($id);

        $this->addFlash('success', "Le produit à bien été ajouté au panier" );

        if($request->query->get('returnToCart')){
            return $this->redirectToRoute("cart_show");
        }

        return $this->redirectToRoute('product_show', [
            'slug' =>$product->getSlug(),
            'category_slug' => $product->getCategory()->getSlug(),
        ]);
    }


    #[Route('/cart', name:'cart_show')]
    public function show(): Response
    {
        $form = $this->createForm(CartConfirmationType::class);

        $detailedCart = $this->cartService->getDetailedCartItems();

        $total = $this->cartService->getTotal();

        return $this->render('cart/index.html.twig', [
            'items' => $detailedCart,
            'total' => $total,
            'confirmationForm' => $form->createView(),
        ]);
    }

    #[Route('/cart/delete/{id}', name:"cart_delete", requirements:["id" => "\d+"])]
    public function delete(int $id)
    {
        $product = $this->productRepository->find($id);

        if(!$product){
            throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut pas être supprimé !");
        }

        $this->cartService->remove($id);

        $this->addFlash("success", "Le produit a bien été supprimé du panier");
        return $this->redirectToRoute("cart_show");
    }

    #[Route('/cart/decrement/{id}', name:"cart_decrement", requirements:["id" => "\d+"])]
    public function decrement($id)
    {

        $product = $this->productRepository->find($id);

        if(!$product){
            throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut pas être supprimé !");
        }
        $this->cartService->decrement($id);

        $this->addFlash("success", "Le produit a bien été retiré du panier");
        return $this->redirectToRoute("cart_show");
    }
}
