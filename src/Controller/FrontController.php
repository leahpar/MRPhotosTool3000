<?php

namespace App\Controller;

use App\Entity\Galerie;
use App\Entity\Modele;
use App\Entity\Photo;
use App\Entity\Shooting;
use App\Service\ZipService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class FrontController extends AbstractController
{
    #[Route(path: '/', name: 'index')]
    public function index(EntityManagerInterface $em): Response
    {
        $photos = $em->getRepository(Photo::class)->findByGalerieIsFront();
        shuffle($photos);

        $galeries = $em->getRepository(Galerie::class)->findBy(["isCover" => true]);
        /** @var Galerie $galerie */
        $galerie = $galeries[random_int(0, count($galeries)-1)];
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

    #[Route(path: '/shootings', name: 'front_shootings')]
    #[IsGranted('ROLE_MODELE')]
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
     * @TODO ShootingAccessVoter
     */
    #[Route(path: '/shootings/{slug}', name: 'front_shooting')]
    #[IsGranted('ROLE_MODELE')]
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
            'type' => 'shooting',
        ]);
    }

    /**
     * @TODO ShootingAccessVoter
     */
    #[Route(path: '/shootings/{slug}/zip', name: 'front_shooting_zip')]
    #[IsGranted('ROLE_MODELE')]
    public function shootingZip(Shooting $shooting, ZipService $zipService): Response
    {
        /** @var Modele $user */
        $user = $this->getUser();

        if (!$this->isGranted("ROLE_ADMIN")
            && !$shooting->hasModele($user)
        ) {
            throw new \Exception("Accès interdit", 403);
        }

        $filename = $shooting->getSlug().".zip";
        $file = $zipService->zip($shooting, $filename);

        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $filename);

        return $response;
    }

    #[Route(path: '/galeries')]
    public function galeries(): Response
    {
        return $this->redirectToRoute("index");
    }

    #[Route(path: '/galeries/{slug}', name: 'front_galerie')]
    public function galerie(Galerie $galerie): Response
    {
        // @TODO GalerieAccessVoter ?
        if ($galerie->getStatut() != "Public") {
            throw new \Exception("Accès interdit", 403);
        }

        return $this->render('front/shooting.html.twig', [
            'shooting' => $galerie,
            'type' => 'galerie',
        ]);
    }

    #[Route(path: '/politique-de-confidentialite', name: 'privacy')]
    public function privacy(): Response
    {
        return $this->render('front/privacy.html.twig');
    }
}
