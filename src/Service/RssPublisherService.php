<?php

namespace App\Service;

use App\Entity\Photo;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;

class RssPublisherService implements PublisherInterface
{
    public function __construct(
        private readonly string $publicDirectory,
        private readonly UrlGeneratorInterface $router
    ) {}

    public function publish(Photo $photo)
    {
        $link = $this->router->generate('app_photo_photo', [
            "slug" => $photo->getShooting()->getSlug(),
            "file" => $photo->getFile(),
            "filter" => "instagram",
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $desc = str_replace("\n", "<br>", $this->getDescription($photo));

        $xml = $this->createXml($link, $desc);
        file_put_contents($this->publicDirectory."/publication.rss", $xml);
    }

    private function getDescription(Photo $photo): string
    {
        $shooting = $photo->getShooting();

        $modeles = [];
        foreach ($shooting->getModeles() as $modele) {
            $str = $modele->getPseudo() ?? $modele->getNom();
            if ($modele->getInstagram()) {
                $str .= " @".$modele->getInstagram();
            }
            $modeles[] = $str;
        }

        $tags = [];
        $tags[] = $photo->getMoreTags();
        foreach ($photo->getTags() as $tag) {
            $tags[] = $tag->getTags();
        }

        $str = "";
        $str .= $photo->getDescription();
        $str .= "\n";
        $str .= "⭐️ ".implode(", ", $modeles)." ⭐️";
        $str .= "\n";
        $str .= "\n";
        $str .= implode(" ", $tags);

        return $str;
    }

    private function createXml(string $link, string $desc): string
    {

        $date = date(\DateTime::RSS);
        $uuid = Uuid::v4();

        return <<<TAG
<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
<channel>
 <title>MR-Photographes</title>
 <link>https://www.mr-photographes.fr/</link>
 <copyright>2021 mr-photographes.fr</copyright>
 <lastBuildDate>$date</lastBuildDate>
 <pubDate>$date</pubDate>
 <ttl>1800</ttl>

 <item>
  <title>MR-Photographes photo de la journée</title>
  <description><![CDATA[$desc]]></description>
  <link>$link</link>
  <guid isPermaLink="false">$uuid</guid>
  <pubDate>$date</pubDate>
 </item>

</channel>
</rss>
TAG;

    }

}
