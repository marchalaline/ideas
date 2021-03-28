<?php

namespace App\Repository;

use App\Entity\Idea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Idea|null find($id, $lockMode = null, $lockVersion = null)
 * @method Idea|null findOneBy(array $criteria, array $orderBy = null)
 * @method Idea[]    findAll()
 * @method Idea[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IdeaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Idea::class);
    }

    public function findIdeasOrderPerNote($userId)
    {
        $qb = $this->createQueryBuilder('i');

        $qb ->leftJoin('i.votes','votes', 'WITH', 'v.user = :userId')
            ->addSelect('votes')
            ->orderBy('i.note' , 'DESC')
            ->setParameter('userId', $userId);

        $query = $qb->getQuery();

        return $query->getResult();
    }

}
