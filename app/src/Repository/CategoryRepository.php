<?php

namespace App\Repository;

use App\Entity\Author;
use App\Entity\Category;
use App\Entity\Settings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

//    /**
//     * @return Category[] Returns an array of Category objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Category
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * @param Category $entity
     * @param bool $flush
     * @return void
     */
    public function add(Category $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getRootCategories(): mixed
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.parent IS NULL')
            ->orderBy('c.id', 'DESC')
        ;

        return $query->getQuery()->getResult();
    }

    public function getChildrenCategories(Category $category): array
    {
        $query = $this->createQueryBuilder('c')
            ->select('c.id as id, IDENTITY(c.parent) as parent_id')
            ->where('c.parent IS NOT NULL')
            ->orderBy('c.id', 'DESC')
        ;

        $listCategory = $query->getQuery()->getResult();

        $result = [
            $category->getId(),
        ];

        // будем проходиться по массиву несколько раз
        // на каждой итерации будем добавлять ДОЧЕРНИЕ категории, если их родителей мы уже добавили в $result
        // и проходимся заново
        // если в фориче мы ни разу не найдём дочерние категории, то перестаём выполнять ду вайл
        do {
            $find = false;

            foreach ($listCategory as $i => $cat) {
                if (in_array($cat['parent_id'], $result)) {
                    $result[] = $cat['id'];
                    unset($listCategory[$i]);
                    // необходимо будет пройтись ещё раз по $listCategory
                    // потому что добавленная категория, может быть родительской для других категорий
                    $find = true;
                }
            }

        } while ($find);


        return $result;
    }

}
