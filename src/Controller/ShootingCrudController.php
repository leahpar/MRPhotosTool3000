<?php

namespace App\Controller;

use App\Entity\Shooting;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class ShootingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Shooting::class;
    }

    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {}

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

            // the max number of entities to display per page
            ->setPaginatorPageSize(30)

            ->setDefaultSort(['date' => 'DESC'])
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $action1 = Action::new('Upload', '', 'fa fa-upload')
            ->linkToRoute('admin_upload', fn(Shooting $shooting) => [
                'id' => $shooting->getId(),
            ]);

        $action2 = Action::new('Photos', '', 'fa fa-images')
            ->linkToUrl(fn(Shooting $shooting) => $this->adminUrlGenerator
                    ->setController(PhotoCrudController::class)
                    ->setAction('index')
                    ->set('query', $shooting->getNom())
                    ->generateUrl()
            );

        return $actions
            ->add(Crud::PAGE_INDEX, $action1)
            ->add(Crud::PAGE_INDEX, $action2)
        ;
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
                //"Public" => "Public",
            ]),
            Field\AssociationField::new('photos')
                ->onlyOnIndex(),
        ];
    }
}
