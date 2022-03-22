<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductType;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;

class UserController extends AbstractController
{
    #[Route('/', name: 'app_user')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $user = new User();
        $users = $doctrine->getRepository(User::class)->findAll();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            ;
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_product');
        }

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form->createView(),
            'users' => $users
        ]);
    }

    #[Route('/list', name: 'users')]
    public function list(Request $request, ManagerRegistry $doctrine): Response
    {
        $user = new User();
        $users = $doctrine->getRepository(User::class)->findAll();
        $usersWithProducts = [];
        foreach ($users as $user) {
            $usersWithProducts[] = $user->getUserWithProducts();
        }

        $product = new Product();
        $products = $doctrine->getRepository(Product::class)->findAll();

        return $this->render('user/list.html.twig', [
            'controller_name' => 'UserController',
            'users' => $usersWithProducts,
            'products' => $products
        ]);
    }
}
