<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Filter\DateSemaineFilter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field;

class PublicationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Publication::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud

            ->setDefaultSort(['date' => 'ASC'])

            // don't forget to add EasyAdmin's form theme at the end of the list
            // (otherwise you'll lose all the styles for the rest of form fields)
            // each template override previous template
            //->setFormThemes(['@EasyAdmin/crud/form_theme.html.twig', 'eadmin/_form.html.twig'])

            //->overrideTemplates([
            //    'crud/field/association' => 'eadmin/template_images_form.html.twig'
            //])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(DateSemaineFilter::new('date', "Date de publication"))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX === $pageName) {
            return [

                Field\DateField::new('date'),

                //Field\ImageField::new('photos')
                //    ->setTemplatePath('eadmin/template_images_list.html.twig'),

                Field\ArrayField::new('thumbnails')
                    ->setTemplatePath('eadmin/template_images_list.html.twig'),

                Field\AssociationField::new('tags')
                    ->setTemplatePath('eadmin/template_entities_list.html.twig')
                    ->setTextAlign('left'),

                Field\TextField::new('description'),

                Field\TextField::new('moreTags')
                    ->setLabel("Tags additionnels"),

                // https://stackoverflow.com/questions/62953123/easyadmin-3-nested-forms
//            Field\CollectionField::new('galeries')
//                ->allowAdd()
//                ->allowDelete()
//                ->setEntryIsComplex(true)
//                ->setEntryType(GalerieType::class)
//                ->setFormTypeOption('by_reference', false)
//                ->onlyOnForms(),
            ];
        }
        elseif (in_array($pageName, [Crud::PAGE_NEW, CRUD::PAGE_EDIT])) {
            return [
                Field\DateField::new('date'),

                Field\AssociationField::new('photos')
                    ->setFormTypeOption('by_reference', false),

                Field\TextareaField::new('description'),

                Field\AssociationField::new('tags')
                    // Doctrine: Cascading Relations and saving the "Inverse" side
                    // https://symfony.com/doc/current/form/form_collections.html
                    // https://github.com/EasyCorp/EasyAdminBundle/issues/860#issuecomment-192605475
                    ->setFormTypeOption('by_reference', false)
                    ->setTextAlign('left'),

                Field\TextField::new('moreTags')
                    ->setLabel("Tags additionnels"),

                // https://stackoverflow.com/questions/62953123/easyadmin-3-nested-forms
//            Field\CollectionField::new('galeries')
//                ->allowAdd()
//                ->allowDelete()
//                ->setEntryIsComplex(true)
//                ->setEntryType(GalerieType::class)
//                ->setFormTypeOption('by_reference', false)
//                ->onlyOnForms(),
            ];
        }

        else {
            return [];
        }
    }
}
