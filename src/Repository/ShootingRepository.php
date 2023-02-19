<?php

namespace App\Repository;

use App\Entity\Modele;
use App\Entity\Shooting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Shooting>
 *
 * @method Shooting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shooting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shooting[]    findAll()
 * @method Shooting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShootingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shooting::class);
    }

    public function searchByModele(Modele $modele)
    {
        $query = $this->createQueryBuilder('s');

        if ($modele->hasRole("ROLE_ADMIN")) {
            // Pas de filtre
        }
        else {
            $query
                ->leftJoin('s.modeles', 'm')
                ->andWhere('m = :modele')
                ->setParameter(':modele', $modele)
                ->andWhere('s.statut != :statut')
                ->setParameter(':statut', "Brouillon");
        }

        $query->orderBy('s.date', 'DESC');

        return $query->getQuery()->getResult();
    }

}
