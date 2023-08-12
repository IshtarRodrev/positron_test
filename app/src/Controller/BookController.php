<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\SettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class BookController extends AbstractController
{
    #[Route('/book/{id}', name: 'app_book', requirements: ['id' => '\d+'])]
    public function index(Environment $twig, BookRepository $bookRepository, SettingsRepository $settingsRepository, int $id): Response
    {
        echo $id;
        die();
        $paginator = $bookRepository->getRootCategories();
        $settings = $settingsRepository->getSettings();
        $maxPages = ceil($paginator->count() / $settings->getCountBookElement());

        return new Response($twig->render('book/index.html.twig', [
            'categories' => $paginator,
            'maxPages' => $maxPages,
            'currentPage' => $page,
        ]));
    }
}
