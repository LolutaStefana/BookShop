<?php
namespace App\Repository;
use App\Entity\Book;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }
    public function findPaginatedBooks($page, $itemsPerPage, User $user)
    {
        $qb = $this->createQueryBuilder('b')
            ->orderBy('b.id', 'ASC')
            ->setMaxResults($itemsPerPage)
            ->setFirstResult(($page - 1) * $itemsPerPage);
        /** @var Book[] $books */
        $books = $qb->getQuery()->getResult();
        $expressionBuilder = Criteria::expr();
        $expression = $expressionBuilder->eq('id', $user->getId());
        $criteria=new Criteria($expression);
        foreach ($books as $book) {
            $book->likingUsers=$book->likingUsers->matching($criteria);
        }
        return $books;
    }
}
