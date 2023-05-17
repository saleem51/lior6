<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasySlugger\Slugger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/admin/category/create', name: 'app_category_create')]
    public function create(Request $request, Slugger $slugger, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $category->setSlug(strtolower($slugger->slugify($category->getName())));

            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('homepage');
        }
        $formView = $form->createView();



        return $this->render('category/create.html.twig', [
            'formView' => $formView,
        ]);
    }

    #[Route('/admin/category/{id}/edit', name: 'app_category_edit')]
    public function edit(int $id, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $category = $categoryRepository->find($id);

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted()){

            $entityManager->flush();

            return $this->redirectToRoute('app_category_display');
        }

        $formView = $form->createView();


        return $this->render('category/edit.html.twig', [
                'category' => $category,
                'formView' => $formView,
        ]);
    }

    #[Route('/categories', name: 'app_category_display')]
    public function display(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('category/display.html.twig', [
            'categories' => $categories,
        ]);
    }

}
