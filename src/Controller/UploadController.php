<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Shooting;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UploadController extends AbstractController
{

    /**
     * @Route("/admin/shootings/{id}/upload", name="admin_upload")
     * @return Response
     */
    public function index(Shooting $shooting, EntityManagerInterface $em)
    {
        return $this->render('upload/index.html.twig', [
            "shooting" => $shooting,
        ]);
    }

    /**
     * @Route("/admin/shootings/{id}/upload/img", name="admin_upload_img", methods={"POST"})
     * @return Response
     */
    public function upload(Request $request, Shooting $shooting, EntityManagerInterface $em)
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('file');

        if ($uploadedFile) {
            $filename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_BASENAME);

            $dir = $this->getParameter('shootings_directory');
            $dir .= "/";
            $dir .= $shooting->getSlug();

            // Move the file to the directory where brochures are stored
            try {
                $uploadedFile->move($dir, $filename);

                // Lecture infos image (IPTC)
                [$width, $height] = getimagesize($dir .'/'. $filename, $infos);
                $iptc = iptcparse($infos['APP13']??null);
                $motsCles = [];
                if ($iptc) {
                    $motsCles = $iptc["2#025"] ?? [];
                }
                //dump($infos, $iptc, $iptc["2#025"]);
                $ratio = round($height/$width,3);

                // Les exifs ne contiennent pas les mots clés de LR
                //$exif = exif_read_data($dir .'/'. $filename);
                //dump($exif);
                //$width = $exif["COMPUTED"]["Width"];
                //$height = $exif["COMPUTED"]["Height"];
                //$ratio = round($height/$width,3);

                $photo = new Photo();
                $photo->setShooting($shooting);
                $photo->setFile($filename);
                $photo->setRatio($ratio);
                $photo->setMotsCles(implode(", ", $motsCles));
                $em->persist($photo);
                $em->flush();

            }
            catch (FileException) {
                // ... handle exception if something happens during file upload
                //dump($e);
            }

        }

        return new Response();
    }

}
