<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart/add/{id}', name: 'app_cart', requirements: ['id' => '\d+'])]
    public function add($id, SessionInterface $session, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);

        if(!$product){
            throw $this->createNotFoundException("Le produit $id n'existe pas");
        }

        $cart = $session->get('cart', []);

        if(array_key_exists($id, $cart)){
            $cart[$id]++;
        }else{
            $cart[$id] = 1;
        }

        $session->set('cart', $cart);

 /*       /**
         * @var FlashBag $flashBag

        $flashBag = $session->getBag('flashes');

        $flashBag->add('success', "Le produit à bien été ajouté au panier");*/

        $this->addFlash('success', "Le produit à bien été ajouté au panier" );


        return $this->redirectToRoute('product_show', [
            'slug' =>$product->getSlug(),
            'category_slug' => $product->getCategory()->getSlug(),
        ]);
    }


    #[Route('/cart', name:'cart_show')]
    public function show(SessionInterface $session): Response
    {

        //dd($session->get('cart'));
        return $this->render('cart/index.html.twig');
    }
}
