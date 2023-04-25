<?php

namespace App\Repository;

use App\Entity\Photo;
use App\Entity\Shooting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Photo>
 *
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

    public function findOneByShootingSlugAndFile(string $slug, string $filename): ?Photo
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.shooting', 's')
            ->andWhere('s.slug = :slug')
            ->andWhere('p.file = :filename')
            ->setParameter(':slug', $slug)
            ->setParameter(':filename', $filename)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
