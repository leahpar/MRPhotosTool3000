<?php

namespace App\Controller;

use App\Entity\Galerie;
use App\Entity\Photo;
use App\Entity\Publication;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Routing\Generator\UrlGenerator;

class PhotoCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return Photo::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the names of the Doctrine entity properties where the search is made on
            // (by default it looks for in all properties)
            // use dots (e.g. 'seller.email') to search in Doctrine associations
            ->setSearchFields(['file', 'motsCles', 'shooting.nom', 'galeries.nom'])

            // the max number of entities to display per page
            ->setPaginatorPageSize(30)

            // these are advanced options related to Doctrine Pagination
            // (see https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/tutorials/pagination.html)
            //->setPaginatorUseOutputWalkers(true)
            //->setPaginatorFetchJoinCollection(true)
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->update(
            Crud::PAGE_INDEX,
            Action::DELETE,
            fn (Action $a) => $a->setIcon('fa fa-trash-alt')->setLabel(false)
        );
        $actions->update(
            Crud::PAGE_INDEX,
            Action::EDIT,
            fn (Action $a) => $a->setIcon('fa fa-pencil-alt')->setLabel(false)
        );

        $actions->add(
            Crud::PAGE_INDEX,
            Action::new('Censure')
                ->linkToUrl(fn(Photo $p) => "admin/photo/".$p->getId()."/censure")
                //->addCssClass('btn btn-primary')
                ->setIcon('fa fa-eye-slash')
                ->displayAsLink()
                //->setHtmlAttributes(["target" => "_blank"])
                ->setLabel(false)
        );


        $actions->add(
            Crud::PAGE_INDEX,
            Action::new('Galerie Front')
            ->createAsBatchAction()
            ->linkToCrudAction('addToGalerieFront')
            ->addCssClass('btn btn-primary')
            ->setIcon('fa fa-bookmark')
        );
        $actions->add(
            Crud::PAGE_INDEX,
            Action::new('Galerie Covers')
            ->createAsBatchAction()
            ->linkToCrudAction('addToGalerieCovers')
            ->addCssClass('btn btn-primary')
            ->setIcon('fa fa-bookmark')
        );
        $actions->add(
            Crud::PAGE_INDEX,
            Action::new('Galerie Couvs')
            ->createAsBatchAction()
            ->linkToCrudAction('addToGalerieCouvs')
            ->addCssClass('btn btn-primary')
            ->setIcon('fa fa-bookmark')
        );
        $actions->add(
            Crud::PAGE_INDEX,
            Action::new('Publication')
            ->createAsBatchAction()
            ->linkToCrudAction('publishPhotos')
            ->addCssClass('btn btn-primary')
            ->setIcon('fa fa-bullhorn')
        );

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            Field\ImageField::new('photo')
                ->setTemplatePath('eadmin/_photocrud_index_field_photo.html.twig')
                ->onlyOnIndex(),

            Field\AssociationField::new('shooting')
                ->onlyOnForms(),

            // https://stackoverflow.com/questions/62953123/easyadmin-3-nested-forms
//            Field\CollectionField::new('galeries')
//                ->allowAdd()
//                ->allowDelete()
//                ->setEntryIsComplex(true)
//                ->setEntryType(GalerieType::class)
//                ->setFormTypeOption('by_reference', false)
//                ->onlyOnForms(),
            Field\AssociationField::new('galeries')
                ->setTemplatePath('eadmin/template_entities_list.html.twig')
                // Doctrine: Cascading Relations and saving the "Inverse" side
                // https://symfony.com/doc/current/form/form_collections.html
                // https://github.com/EasyCorp/EasyAdminBundle/issues/860#issuecomment-192605475
                ->setFormTypeOption('by_reference', false)
                ->setTextAlign('left')
                //->addCssClass(fn(Photo $p) => $p->isCouv() ? "couv" : "" )
                ->onlyOnForms()
            ,

            Field\TextField::new('motsCles'),

            //Field\DateField::new('datePlanifiee')
            //    //->setFormat()
            //    ->onlyOnIndex(),

            //Field\DateField::new('datePublication')
            //    ->onlyOnIndex(),

            Field\TextareaField::new('description')
                ->onlyOnForms(),

            Field\AssociationField::new('tags')
                // Doctrine: Cascading Relations and saving the "Inverse" side
                // https://symfony.com/doc/current/form/form_collections.html
                // https://github.com/EasyCorp/EasyAdminBundle/issues/860#issuecomment-192605475
                ->setFormTypeOption('by_reference', false)
                ->setTextAlign('left')
                ->onlyOnForms(),

            Field\TextField::new('moreTags')
                ->setLabel("Tags additionnels")
                ->onlyOnForms(),

            //Field\ArrayField::new('censure')
            //    ->onlyOnIndex(),

        ];
    }

    public function addToGalerie(array $ids, Galerie $galerie, AdminContext $context)
    {
        $entityClass = $context->getEntity()->getFqcn();
        $em = $this->getDoctrine()->getManagerForClass($entityClass);

        foreach ($ids as $id) {
            $photo = $em->find($entityClass, $id);
        }

        //$em->flush();

        return $this->redirect($context->getReferrer());
    }


    public function addToGalerieFront(array $ids, AdminContext $context)
    {
        $entityClass = $context->getEntity()->getFqcn();
        $em = $this->getDoctrine()->getManagerForClass($entityClass);
        $galerie = $em->getRepository(Galerie::class)->findOneBy(["isFront" => true]);

        foreach ($ids as $id) {
            $photo = $em->getRepository(Photo::class)->find($id);
            $galerie->addPhoto($photo);
        }
        $em->flush();

        return $this->redirect($context->getReferrer());
    }

    public function addToGalerieCouvs(array $ids, AdminContext $context)
    {
        $entityClass = $context->getEntity()->getFqcn();
        $em = $this->getDoctrine()->getManagerForClass($entityClass);
        $galerie = $em->getRepository(Galerie::class)->findOneBy(["isCouv" => true]);

        foreach ($ids as $id) {
            $photo = $em->getRepository(Photo::class)->find($id);
            $galerie->addPhoto($photo);
        }
        $em->flush();

        return $this->redirect($context->getReferrer());
    }

    public function addToGalerieCovers(array $ids, AdminContext $context)
    {
        $entityClass = $context->getEntity()->getFqcn();
        $em = $this->getDoctrine()->getManagerForClass($entityClass);
        $galerie = $em->getRepository(Galerie::class)->findOneBy(["isCover" => true]);

        foreach ($ids as $id) {
            $photo = $em->getRepository(Photo::class)->find($id);
            $galerie->addPhoto($photo);
        }
        $em->flush();

        return $this->redirect($context->getReferrer());
    }

    public function publishPhotos(array $ids, AdminContext $context)
    {
        $publication = new Publication();

        $entityClass = $context->getEntity()->getFqcn();
        $em = $this->getDoctrine()->getManagerForClass($entityClass);

        // Dernière publication
        $lastPubs = $em->getRepository(Publication::class)->findBy([], ['date' => "desc"], 1);
        $lastPub = $lastPubs[0] ?? null;
        /** @var Publication $lastPub */
        if ($lastPub) {
            $date = (clone ($lastPub->getDate()))->modify("monday next week");
            $publication->setDate($date);
        }

        foreach ($ids as $id) {
            $photo = $em->getRepository(Photo::class)->find($id);
            $publication->addPhoto($photo);
        }
        $em->persist($publication);
        $em->flush();

        /** @var AdminUrlGenerator $adminUrlGenerator */
        $adminUrlGenerator = $this->get(AdminUrlGenerator::class);
        $url = $adminUrlGenerator
            ->setController(PublicationCrudController::class)
            ->setAction(ACTION::EDIT)
            ->setEntityId($publication->getId())
            ->generateUrl();

        return $this->redirect($url);
    }



}
