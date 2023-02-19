<?php

namespace App\Repository;

use App\Entity\Stat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stat>
 *
 * @method Stat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stat[]    findAll()
 * @method Stat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stat::class);
    }

    public function stats(\DateTime $from, string $name)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.date >= :date')
            ->setParameter(':date', $from)
            ->andWhere('s.name = :name')
            ->setParameter(':name', $name)
            ->orderBy('s.date', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

}
