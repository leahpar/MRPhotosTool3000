<?php


namespace App\Controller;


use App\Entity\Modele;
use App\Entity\Photo;
use App\Entity\Shooting;
use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Service\FilterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Component\Routing\Annotation\Route;

class PhotoController extends AbstractController
{
    /**
     * @Route("shootings/{slug}/{file}")
     *
     * @TODO: "304 Not Modified" ?
     *
     * @param Shooting $shooting
     * @param Photo $photo
     * @return Response
     */
    public function photo(Request $request, Shooting $shooting, Photo $photo, FilterService $imagine, ?Profiler $profiler): Response
    {
        $filter = $request->query->get('filter', 'thumbnail');

        // Accès instagram autorisé sur les photos publiées
        if ($filter != "instagram" || !$photo->isPublished()) {
            // Sinon, contrôle d'accès classique
            $this->denyAccessUnlessGranted('view', $photo);
            // $this->createAccessDeniedException();
        }

        /** @var string */
        $path = $shooting->getSlug().'/'.$photo->getFile();

        if ($profiler) {
            // Désactivation du profiler, inutile ici
            // https://symfony.com/doc/current/profiler.html#enabling-the-profiler-conditionally
            // (voir aussi config/services_dev.yaml)
            $profiler->disable();
        }

        if ($filter == "instagram") {
            list($width, $height, $type, $attr) = getimagesize($this->getParameter('shootings_directory') . '/' . $path);
            $ratioImg = $height / $width;
            $ratioInsta = 5 / 4;

            $runtimeConfig = [
                'background' => [
                    'size' => [
                        1080,
                        (int)min(1350, 1080 * $ratioImg),
                    ]
                ],
            ];

            dump($photo, $photo->getCensure());
            if (count($photo->getCensure()??[]) > 0) {
                $runtimeConfig += [
                    'censure_filter' => [
                        'enabled' => true,
                        'positions' => $photo->getCensure()
                    ],
                ];
            }

            $resourcePath = $imagine->getUrlOfFilteredImageWithRuntimeFilters(
                $path,
                'instagram',
                $runtimeConfig
            );
            $filename = parse_url($resourcePath, PHP_URL_PATH);
            $file = $this->getParameter('public_directory') . $filename;
        }
        elseif ($filter == "full") {
            $resourcePath = $this->getParameter('shootings_directory') . '/' . $path;
            //$filename = parse_url($resourcePath, PHP_URL_PATH);
            //$file = $this->getParameter('public_directory') . $filename;
            $file = $resourcePath;
        }
        else {
            $resourcePath = $imagine->getUrlOfFilteredImage($path, $filter);
            $filename = parse_url($resourcePath, PHP_URL_PATH);
            $file = $this->getParameter('public_directory') . $filename;
        }


        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $photo->getFile());

        // Pour mettre en cache (navigateur mais pas proxy (=> private)) les images
        $response->setPrivate();
        $response->setMaxAge(86400);
        // Cache géré manuellement, on désactive la gestion par symfony
        $response->headers->set('Symfony-Session-NoAutoCacheControl', 'true');

        return $response;
    }


    /**
     * //Route("shootings/{file}")
     * //Security("is_granted('view', photo)")
     *
     * @param Photo $photo
     * @param FilterService $imagine
     * @return Response
     */
    public function photoDirect(Photo $photo, FilterService $imagine): Response
    {
        $this->denyAccessUnlessGranted('view', $photo);

        /** @var string */
        $path = $photo->getShooting()->getSlug().'/'.$photo->getFile();

        $resourcePath = $imagine->getUrlOfFilteredImage($path, 'thumbnail');

        $filename = parse_url($resourcePath, PHP_URL_PATH);
        dump($filename);
        $file = $this->getParameter('public_directory') . $filename;

        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $photo->getFile());

        // Pour mettre en cache (client & proxys) les images
        //$response->setPublic();
        //$response->setMaxAge(86400);
        $response->setSharedMaxAge(86400);
        // Cache géré manuellement, on désactive la gestion par symfony
        $response->headers->set('Symfony-Session-NoAutoCacheControl', 'true');

        // Pour mettre en cache (navigateur mais pas proxy (=> private)) les images
        $response->setPrivate();
        $response->setMaxAge(86400);
        // Cache géré manuellement, on désactive la gestion par symfony
        $response->headers->set('Symfony-Session-NoAutoCacheControl', 'true');

        return $response;
    }

    /**
     * Applique un floutage
     *
     * @Route("admin/photo/{id}/censure", name="photo_censure")
     * @param Photo $photo
     * @param EntityManagerInterface $em
     */
    public function photoCensurePosition(Request $request, Photo $photo, EntityManagerInterface $em, FilterService $imagine)
    {
        /** @var array $arr */
        $arr = $request->query->all();

        dump($arr);

        if (isset($arr["delete"])) {
            dump("here");
            $photo->setCensure(null);
            $em->flush();

            $path = $photo->getShooting()->getSlug() . '/' . $photo->getFile();
            $resourcePath = $imagine->getUrlOfFilteredImage($path, 'thumbnail');
            $filename = parse_url($resourcePath, PHP_URL_PATH);
            $file = $this->getParameter('public_directory') . $filename;
            unlink($file);
        }
        else if (count($arr) > 0) {
            dump("here2");
            $arr = array_flip($arr);
            $pos = array_shift($arr);
            //$pos = explode(',', $pos);
            $photo->addCensure($pos);
            $em->flush();

            $path = $photo->getShooting()->getSlug() . '/' . $photo->getFile();
            $resourcePath = $imagine->getUrlOfFilteredImage($path, 'thumbnail');
            $filename = parse_url($resourcePath, PHP_URL_PATH);
            $file = $this->getParameter('public_directory') . $filename;
            unlink($file);
        }

        return $this->render('admin/photo_censure.html.twig', [
            'photo' => $photo
        ]);
    }

}
