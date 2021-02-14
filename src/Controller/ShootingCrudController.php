<?php

namespace App\Controller;

use App\Entity\Modele;
use App\Entity\Shooting;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field;

class ShootingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Shooting::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        // https://symfony.com/doc/current/bundles/EasyAdminBundle/crud.html

        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Shooting')
            ->setEntityLabelInPlural('Shootings')

            // the Symfony Security permission needed to manage the entity
            // (none by default, so you can manage all instances of the entity)
            //->setEntityPermission('ROLE_EDITOR')
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $action = Action::new('upload', '', 'fa fa-upload')
            ->linkToRoute('admin_upload', function (Shooting $shooting) {
                return [
                    'id' => $shooting->getId(),
                ];
            });

        return $actions->add(Crud::PAGE_INDEX, $action);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            'date',
            'bookshoot',
            'nom',
            Field\AssociationField::new('modeles')
                ->setTemplatePath('eadmin/template_entities_list.html.twig')
                ->setTextAlign('left'),
            Field\ChoiceField::new('statut')->setChoices([
                "Brouillon" => "Brouillon",
                "Privé" => "Privé",
                "Public" => "Public",
            ]),
            Field\AssociationField::new('photos')
                ->onlyOnIndex(),
        ];
    }
}
