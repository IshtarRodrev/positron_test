<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\SettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Environment $twig, CategoryRepository $categoryRepository, SettingsRepository $settingsRepository): Response
    {
        //$settings = $settingsRepository->getSettings();
        $roots = $categoryRepository->getRootCategories();

        return new Response($twig->render('home/index.html.twig', [
            'categories' => $roots,
        ]));
    }
}
