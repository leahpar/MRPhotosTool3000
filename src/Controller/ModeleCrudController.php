<?php

namespace App\Controller;

use App\Entity\Modele;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field;

class ModeleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Modele::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        // https://symfony.com/doc/current/bundles/EasyAdminBundle/crud.html

        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Modèle')
            ->setEntityLabelInPlural('Modèles')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            'nom',
            'pseudo',
            'instagram',

            Field\FormField::addPanel("Connexion")
                ->onlyOnForms(),
            Field\TextField::new('username')
                ->setLabel("Login")
                ->onlyOnForms(),
            Field\textField::new('plainPassword')
                ->setLabel("Nouveau mot de passe")
                ->onlyOnForms(),
        ];
    }

}

