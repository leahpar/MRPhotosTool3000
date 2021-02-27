<?php

namespace App\Controller;

use App\Entity\Galerie;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

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
            Field\TextField::new('slug')
                ->setTemplatePath('eadmin/_galeriecrud_index_slug.html.twig')
            ,
            Field\AssociationField::new('photos'),
            Field\ChoiceField::new('statut')->setChoices([
                //"Brouillon" => "Brouillon",
                "Privé" => "Privé",
                "Public" => "Public",
            ]),

        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        /** @var AdminUrlGenerator $adminUrlGenerator */
        $adminUrlGenerator = $this->get(AdminUrlGenerator::class);

        $action = Action::new('Photos', '', 'fa fa-images')
            ->linkToUrl(function (Galerie $galerie) use ($adminUrlGenerator) {
                return $adminUrlGenerator//->build()
                    ->setController(PhotoCrudController::class)
                    ->setAction('index')
                    ->set('query', $galerie->getSlug())
                    ->generateUrl();
            }
        );

        return $actions->add(Crud::PAGE_INDEX, $action);
    }


}
