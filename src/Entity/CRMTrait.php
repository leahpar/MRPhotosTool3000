<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

trait CRMTrait
{

    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\Datetime $dernierContact = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\Datetime $prochainContact = null;

    #[ORM\Column(type: 'boolean')]
    public bool $aSuivre = false;

}
