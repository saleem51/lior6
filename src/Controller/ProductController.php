<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasySlugger\Slugger;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProductController extends AbstractController
{
    #[Route('/{slug}', name: 'product_category')]
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository-> findOneBy([
            'slug' => $slug
        ]);

        // if(!$category) {
        //     throw $this->createNotFoundException("La catégorie demandée n'existe pas !");
        // }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    #[Route('/{category_slug}/{slug}', name:"product_show")]
    public function show($slug, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy([
            'slug' => $slug
        ]);

        if(!$product)
        {
            throw $this->createNotFoundException("Le produit demandé n'existe pas ");
        }

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    #[Route("/admin/product/create", name:"product_create")]
    public function create(FormFactoryInterface $factory, Request $request, Slugger $slugger, EntityManagerInterface $entityManager)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $product->setSlug(strtolower($slugger->slugify($product->getName())));

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug(),
            ]);
        }
        $formView = $form->createView();

        return $this->render('product/create.html.twig', [
            'formView' => $formView,
        ]);
    }

    #[Route('/admin/product/{id}/edit', name: 'product_edit')]
    public function edit(int $id,
                         ProductRepository $productRepository,
                         Request $request,
                         EntityManagerInterface $entityManager
    )
    {
        $product = $productRepository->find($id);/**/

        $form = $this->createForm(ProductType::class, $product);

        //$form->setData($product);

        $form->handleRequest($request);

        if($form->isSubmitted()){
            $entityManager->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug(),
            ]);

        }

        $formView = $form->createView();

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formView' => $formView,
        ]);

    }
}
