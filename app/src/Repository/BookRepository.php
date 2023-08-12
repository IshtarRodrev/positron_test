<?php

namespace App\Repository;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Settings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * @param Book $entity
     * @param bool $flush
     * @return void
     */
    public function add(Book $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param Book $book
     * @param Author $author
     * @param bool $flush
     * @return void
     */
    public function addAuthor(Book $book, Author $author, bool $flush = true): void
    {
        $book->addAuthor($author);
        $author->addBook($book);

        $this->_em->persist($book);
        $this->_em->persist($author);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param Book $book
     * @param Category $category
     * @param bool $flush
     * @return void
     */
    public function addCategory(Book $book, Category $category, bool $flush = true): void
    {
        $book->addCategory($category);
        $category->addBook($book);

        $this->_em->persist($book);
        $this->_em->persist($category);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getBooks(Category $category, array $childrenCategory, Settings $settings, $currentPage = 1): Paginator
    {
        $offset = $settings->getCountBookElement() * ($currentPage - 1);
        $limit = $settings->getCountBookElement();

        $paginator = $this->createQueryBuilder('b')
            ->orderBy('b.title', 'DESC')
            ->innerJoin('b.categories','cb')
            ->Where('cb.id IN (:categories)')
            ->setParameter('categories', $childrenCategory)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
        ;

        return new Paginator($paginator);
    }
}
