<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class BookController extends AbstractController
{
    #[Route('/book/{id}', name: 'app_book', requirements: ['id' => '\d+'])]
    public function index(Environment $twig, BookRepository $bookRepository, int $id): Response
    {
        $book = $bookRepository->find($id);
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
            'book' => $book,
            'related' => $pickRandomTenOrLess
        ]));
    }
}
