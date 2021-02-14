<?php

namespace App\Controller;

use App\Entity\Galerie;
use App\Entity\Modele;
use App\Entity\Photo;
use App\Entity\Shooting;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(EntityManagerInterface $em): Response
    {
        $photos = $em->getRepository(Photo::class)->findByGalerieIsFront();
        shuffle($photos);

        $galeries = $em->getRepository(Galerie::class)->findBy(["isCover" => true]);
        /** @var Galerie $galerie */
        $galerie = $galeries[rand(0, count($galeries)-1)];
        $cover = $galerie->getRandomPhoto();

        $width = 500; // px

        $height = 3000; // px
        $col1 = [];
        while ($height > 0 && count($photos)) {
            /** @var Photo $photo */
            $photo = array_shift($photos);
            $col1[] = $photo;
            $height -= $photo->getRatio()*$width;
        }

        $height = 2500; // px
        $col2 = [];
        while ($height > 0 && count($photos)) {
            /** @var Photo $photo */
            $photo = array_shift($photos);
            $col2[] = $photo;
            $height -= $photo->getRatio()*$width;
        }

        $height = 3000; // px
        $col3 = [];
        while ($height > 0 && count($photos)) {
            /** @var Photo $photo */
            $photo = array_shift($photos);
            $col3[] = $photo;
            $height -= $photo->getRatio()*$width;
        }

        return $this->render('front/index.html.twig', [
            'cover' => $cover,
            'photos1' => $col1,
            'photos2' => $col2,
            'photos3' => $col3,
        ]);
    }

    /**
     * @Route("/galeries", name="front_galeries")
     * @Security("is_granted('ROLE_ADMIN')")
     * @TODO GalerieAccessVoter
     *
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function galeries(EntityManagerInterface $em): Response
    {
        $galerieCouvs = $em->getRepository(Galerie::class)->findAll();
        return $this->render('front/shootings.html.twig', [
            'galerie_couvs' => $galerieCouvs,
        ]);
    }

    /**
     * @Route("/shootings", name="front_shootings")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_MODELE')")
     *
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function shootings(EntityManagerInterface $em): Response
    {
        /** @var Modele $user */
        $user = $this->getUser();

        $shootings = $em->getRepository(Shooting::class)->searchByModele($user);

        return $this->render('front/shootings.html.twig', [
            'shootings' => $shootings,
        ]);
    }

    /**
     * @Route("/shootings/{slug}", name="front_shooting")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_MODELE')")
     * @TODO ShootingAccessVoter
     *
     * @param Shooting $shooting
     * @return Response
     */
    public function shooting(Shooting $shooting): Response
    {
        /** @var Modele $user */
        $user = $this->getUser();

        if (!$this->isGranted("ROLE_ADMIN")
            && !$shooting->hasModele($user)
            && $shooting->getStatut() != "Public"
        ) {
            throw new \Exception("Accès interdit", 403);
        }

        return $this->render('front/shooting.html.twig', [
            'shooting' => $shooting,
        ]);
    }

}
