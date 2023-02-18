<?php


namespace App\Service;


use App\Entity\Photo;
use App\Entity\Publication;
use Doctrine\ORM\EntityManagerInterface;

class PublishService
{
    private array $calendrier = [
        //    lun  mar  mer  jeu  ven  sam  dim
        //    0    1    2    3    4    5    6
        1 => [1,   0,   0,   0,   0,   0,   0   ],  
        2 => [1,   0,   2,   0,   0,   0,   0   ],
        3 => [1,   0,   2,   0,   3,   0,   0   ],
        4 => [1,   2,   0,   3,   4,   0,   0   ],
        5 => [1,   2,   3,   4,   5,   0,   0   ],
        6 => [1,   2,   3,   4,   5,   6,   0   ],
        7 => [1,   2,   3,   4,   5,   6,   7   ],
    ];

    /**
     * PublishService constructor.
     */
    public function __construct(private readonly EntityManagerInterface $em, private readonly PublisherInterface $publisher)
    {
    }

    /**
     * Retourne la publication de la semaine demandée
     * Semaine courante par défaut
     */
    public function getPublicationSemaine(?\DateTime $date = null): ?Publication
    {
        $date = $date ? clone $date : (new \DateTime());
        $date->modify("monday this week");

        $publication = $this->em->getRepository(Publication::class)->findOneBy([
            'date' => $date
        ]);
        return $publication;
    }

    /**
     * Retourne la photo à publier aujourd'hui
     * en fonction du nombre de photos de la publication et du jour de la semaine
     */
    public function getPhotoToPublish(?\DateTime $date = null): ?Photo
    {
        $date = $date ? clone $date : (new \DateTime());

        $publication = $this->getPublicationSemaine($date);

        if (!$publication) {
            //throw new \Exception("Aucune publication planifiée à cette date");
            return null;
        }

        $photos = $publication->getPhotos();

        $cpt = count($photos);
        $jour = $date->format('N')-1;
        $index = $this->calendrier[$cpt][$jour]-1;
        //dump($cpt, $jour, $index);
        return $photos[$index] ?? null;
    }

    public function publishPhoto(Photo $photo)
    {
        $photo->setDatePublication(new \DateTime());
        $this->publisher->publish($photo);
    }

}
