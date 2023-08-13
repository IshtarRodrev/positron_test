<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\SettingsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use phpDocumentor\Reflection\Types\Mixed_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class BookController extends AbstractController
{
    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Twig\Error\LoaderError
     */
    #[Route('/book/{id}', name: 'app_book', requirements: ['id' => '\d+'])]
    public function index(Environment $twig, BookRepository $bookRepository, SettingsRepository $settingsRepository, int $id)//: ArrayCollection
    {
        $book = $bookRepository->find($id);
        $settings = $settingsRepository->getSettings();
//        $maxPages = ceil($paginator->count() / $settings->getCountBookElement());
        $categories = $book->getCategories();
        $related = [];
        foreach ($categories as $category){
            $relatedBooks = $category->getBooks();
            foreach ($relatedBooks as $item) {
                if ($item->getId() !== $id)
                    $related[] = $item;
            }
        }
        shuffle( $related);
        $pickRandomTenOrLess = array_slice($related, 0, 7);

        return new Response($twig->render('book/index.html.twig', [
            'book' => $book ,
//            'maxPages' => $maxPages,
//            'currentPage' => $page,
            'related' => $pickRandomTenOrLess
        ]));
    }
}
