<?php


namespace App\Controller;


use App\Service\PublishService;
use App\Service\RssPublisherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class PublishController extends AbstractController
{

    /**
     * @Route("/test")
     */
    public function cron(PublishService $pubService, RssPublisherService $rssService)
    {
        // https://www.php.net/manual/fr/datetime.formats.relative.php
        $date = new \DateTime("last friday");
        $photo = $pubService->getPhotoToPublish($date);

        if ($photo) {
            $rssService->publish($photo);
        }

        return new Response("<html><head></head><body></body></html>");
    }

}
