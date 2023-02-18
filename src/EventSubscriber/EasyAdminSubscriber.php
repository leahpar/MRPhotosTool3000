<?php

namespace App\EventSubscriber;

use App\Entity\Modele;
use App\Entity\Publication;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    /**
     * EasyAdminSubscriber constructor.
     */
    public function __construct(private readonly UserPasswordEncoderInterface $passwordEncoder, private readonly EntityManagerInterface $em)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => [
                ['encodePassword', 0],
            ],
            BeforeEntityUpdatedEvent::class => [
                ['setPhotosPublicationDetails', 0],
                ['encodePassword', 0],
            ]
        ];
    }

    public function setPhotosPublicationDetails(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Publication)) {
            return;
        }

        // MAJ des données photos
        $photos = $entity->getPhotos();
        foreach ($photos as $photo) {
            $photo->setDescription($entity->getDescription());
            $photo->setMoreTags($entity->getMoreTags());
            $photo->setTags($entity->getTags());
        }

        // Décallage des dates des publications suivantes
        $publications = $this->em->getRepository(Publication::class)->findPublicationsToUpdate($entity);
        $date = clone $entity->getDate();
        /** @var Publication $publication */
        foreach ($publications as $publication) {
            $date->modify("monday next week");
            $publication->setDate(clone $date);
        }
    }

    public function encodePassword($event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Modele)) {
            return;
        }

        if (empty($entity->getPlainPassword())) {
            return;
        }

        $password = $this->passwordEncoder->encodePassword($entity, $entity->getPlainPassword());
        $entity->setPassword($password);
    }

}
