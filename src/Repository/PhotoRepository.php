<?php

namespace App\Repository;

use App\Entity\Photo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Photo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Photo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Photo[]    findAll()
 * @method Photo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Photo::class);
    }

    public function findByGalerieIsFront()
    {
        return $this->createQueryBuilder('p')
            ->leftJoin("p.galeries", 'g')
            ->andWhere('g.isFront = :true')
            ->setParameter(':true', true)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByGalerieIsCover()
    {
        return $this->createQueryBuilder('p')
            ->leftJoin("p.galeries", 'g')
            ->andWhere('g.isCover = :true')
            ->setParameter(':true', true)
            ->getQuery()
            ->getResult()
        ;
    }

    public function stats(\DateTime $from)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.datePublication >= :date')
            ->setParameter(':date', $from)
            ->orderBy('p.datePublication', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

}
