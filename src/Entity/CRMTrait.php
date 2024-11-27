<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

trait CRMTrait
{

    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\Datetime $dateDernierContact = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\Datetime $dateProchainContact = null;

    #[ORM\Column(type: 'boolean')]
    public bool $aSuivre = false;

}
