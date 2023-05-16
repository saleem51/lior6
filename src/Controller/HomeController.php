<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function homepage(ProductRepository $productRepository, EntityManagerInterface $entityManager)
    {
        $produits = $productRepository->findAll();


       /* $product = new Product();*/

        /* $product
             ->setName('Chaise en plexi')
             ->setPrice(4000)
             ->setDescription('Description texte 4')
             ->setSlug('chaise-en-plexi');*/

        //$entityManager->persist($product);
        $entityManager->flush();

        return $this->render('home.html.twig', [
            'produits' => $produits
        ]);
    }

}
