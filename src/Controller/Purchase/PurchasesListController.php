<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Environment;

class PurchasesListController extends AbstractController
{
    public function __construct(){}

    #[Route('/purchases', name:'purchase_index')]
    #[isGranted("ROLE_USER", message : "Vous devez être connecté pour accéder à vos commandes")]
    public function index(): Response
    {
        $user = $this->getUser();

        return $this->render('purchase/index.html.twig', [
            'purchases' => $user->getPurchases(),
        ]);

    }

}