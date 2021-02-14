<?php

namespace App\Controller;

use App\Entity\Galerie;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field;

class GalerieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Galerie::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            Field\TextField::new('nom'),
            Field\AssociationField::new('photos'),
            Field\ChoiceField::new('statut')->setChoices([
                "Brouillon" => "Brouillon",
                "Privé" => "Privé",
                "Public" => "Public",
            ]),

        ];
    }

}
