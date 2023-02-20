<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Stat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    /*
     * https://developers.facebook.com/tools/explorer
     * https://developers.facebook.com/docs/instagram-api/reference/ig-user/
     * https://www.instagram.com/mrphotographes/?__a=1
     */
    #[Route(path: '/admin/stats', name: 'admin_stats')]
    public function index(EntityManagerInterface $em): Response
    {
        $from = (new \DateTime())->modify('-30 days');

        $stats = $em->getRepository(Stat::class)->stats($from, "followers_count");
        $publications = $em->getRepository(Photo::class)->stats($from);

        return $this->render('stats/index.html.twig', [
            'stats' => $stats,
            'publications' => $publications,
        ]);
    }

}
