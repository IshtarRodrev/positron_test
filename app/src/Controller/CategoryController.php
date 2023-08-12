<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Settings;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use App\Repository\SettingsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class CategoryController extends AbstractController
{
    #[Route('/category/view/{id}/{page}', name: 'app_category_view', requirements: ['id' => '\d+', 'page' => '\d+'])]
    public function view(BookRepository $bookRepository, CategoryRepository $categoryRepository, SettingsRepository $settingsRepository, Request $request, ManagerRegistry $doctrine, int $id, int $page = 1): Response
    {
        $entityManager = $doctrine->getManager();
        $category = $categoryRepository->find($id);
        $settings = $settingsRepository->getSettings();

        if (!$category) {
            throw $this->createNotFoundException(
                'No category with ID '. $id . ' found'
            );
        }

        $category = $categoryRepository->find($id);
        $childrenCategory = $categoryRepository->getChildrenCategories($category);

        $paginator = $bookRepository->getBooks($category, $childrenCategory, $settings, $page);
        $maxPages = ceil($paginator->count() / $settings->getCountBookElement());

        $breadcrumbs = $category->getAllParents();


        return $this->renderForm('category/index.html.twig', [
            'category' => $category,
            'children' => $category->getChildren(), // пихаю объекты классов вместо массива??
            'books'    => $category->getBooks($category), // пихаю объекты классов вместо массива??
            'paginator'=> $paginator,
            'maxPages' => $maxPages,
            'currentPage' => $page,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
