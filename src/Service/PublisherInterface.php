<?php

namespace App\Service;

use App\Entity\Photo;

interface PublisherInterface
{
    public function publish(Photo $photo);
}
