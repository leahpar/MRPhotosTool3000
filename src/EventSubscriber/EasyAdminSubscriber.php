<?php

namespace App\EventSubscriber;

use App\Entity\Modele;
use App\Entity\Publication;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use EasyCorp\Bundle\EasyAdminBundle\Event\AbstractLifecycleEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordEncoder,
        private readonly EntityManagerInterface $em
    ) {}

    public static function getSubscribedEvents()
    {
        return [
            //Events::preUpdate => ['encodePassword', 0], // TODO: why not triggered ?
            BeforeEntityPersistedEvent::class => [
                ['encodePassword', 0],
            ],
            BeforeEntityUpdatedEvent::class => [
                ['setPhotosPublicationDetails', 0],
                ['encodePassword', 0],
            ],
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

    public function encodePassword(AbstractLifecycleEvent|PrePersistEventArgs $event)
    {
        $entity = match (true) {
            $event instanceof BeforeEntityPersistedEvent,
            $event instanceof BeforeEntityUpdatedEvent,
            $event instanceof AbstractLifecycleEvent => $event->getEntityInstance(),
            $event instanceof PrePersistEventArgs => $event->getObject(),
            default => null,
        };
        dump($event, $entity);

        if (!($entity instanceof Modele)) {
            return;
        }

        if (empty($entity->getPlainPassword())) {
            return;
        }

        $password = $this->passwordEncoder->hashPassword($entity, $entity->getPlainPassword());
        $entity->setPassword($password);
    }

}
