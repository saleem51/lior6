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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CategoryController extends AbstractController
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function renderMenuList()
    {
        $categories = $this->categoryRepository->findAll();

        return $this->render('category/_menu.html.twig', [
            'categories' => $categories,
        ]);

    }

    #[Route('/admin/category/create', name: 'app_category_create')]
    public function create(Request $request, Slugger $slugger, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted()  && $form->isValid()){
            $category->setSlug(strtolower($slugger->slugify($category->getName())));
            $category->setOwner($this->getUser());

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
    //#[isGranted("CAN_EDIT",subject: "id",message: "C'est pas bon" )]
    public function edit(int $id, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $category = $categoryRepository->find($id);

        if(!$category){
            throw new NotFoundHttpException("Cette catégorie n'existe pas");
        }

        //$this->denyAccessUnlessGranted('CAN_EDIT', $category, "Sans les annotations et pas le propriétaire de la category");
        /*$user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('app_login');
        }
        if($user !== $category->getOwner()){
            throw new AccessDeniedException("Vous n'êtes pas le propriétaire de cette catégorie");
        }*/

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
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
