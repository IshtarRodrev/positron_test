<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Environment $twig, CategoryRepository $categoryRepository): Response
    {
        $roots = $categoryRepository->getRootCategories();

        return new Response($twig->render('home/index.html.twig', [
            'categories' => $roots,
        ]));
    }
}
