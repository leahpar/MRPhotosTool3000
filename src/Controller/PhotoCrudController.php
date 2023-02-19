<?php

namespace App\Controller;

use App\Entity\Galerie;
use App\Entity\Photo;
use App\Entity\Publication;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Field;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class PhotoCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return Photo::class;
    }

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ){}

    public function configureCrud(Crud $crud): Crud
    {
        $searchFields = [
            'file',
            'motsCles',
            'shooting.modeles.pseudo',
            'shooting.modeles.nom',
            'shooting.nom',
            'galeries.slug',
        ];

        return $crud
            // the names of the Doctrine entity properties where the search is made on
            // (by default it looks for in all properties)
            // use dots (e.g. 'seller.email') to search in Doctrine associations
            ->setSearchFields($searchFields)
            ->setHelp("index", "Recherche sur : " . implode(" / ", $searchFields))

            // the max number of entities to display per page
            ->setPaginatorPageSize(48)


            ->setDefaultSort(['shooting.date' => 'DESC', 'file' => 'ASC'])
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

        $actions->addBatchAction(Action::new('Site front')
            ->linkToCrudAction('addToGalerieFront')
            ->addCssClass('btn btn-primary')
            ->setIcon('fa fa-bookmark'))
        ;
        $actions->addBatchAction(Action::new('Site covers')
            ->linkToCrudAction('addToGalerieCovers')
            ->addCssClass('btn btn-primary')
            ->setIcon('fa fa-bookmark'))
        ;
        $actions->addBatchAction(Action::new('Couvs')
            ->linkToCrudAction('addToGalerieCouvs')
            ->addCssClass('btn btn-primary')
            ->setIcon('fa fa-bookmark'))
        ;
        $actions->addBatchAction(Action::new('Publication')
            ->linkToCrudAction('publishPhotos')
            ->addCssClass('btn btn-primary')
            ->setIcon('fa fa-bullhorn'))
        ;

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

            Field\TextField::new('motsCles')
                ->onlyOnForms(),

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

    public function addToGalerie(array $ids, string $galerieType)
    {
        $galerie = $this->em->getRepository(Galerie::class)->findOneBy([$galerieType => true]);
        foreach ($ids as $id) {
            $photo = $this->em->getRepository(Photo::class)->find($id);
            $galerie->addPhoto($photo);
        }
        $this->em->flush();
    }

    public function addToGalerieFront(BatchActionDto $batchActionDto)
    {
        $this->addToGalerie($batchActionDto->getEntityIds(), "isFront");
        return $this->redirect($batchActionDto->getReferrerUrl());
    }

    public function addToGalerieCouvs(BatchActionDto $batchActionDto)
    {
        $this->addToGalerie($batchActionDto->getEntityIds(), "isCouv");
        return $this->redirect($batchActionDto->getReferrerUrl());
    }

    public function addToGalerieCovers(BatchActionDto $batchActionDto)
    {
        $this->addToGalerie($batchActionDto->getEntityIds(), "isCover");
        return $this->redirect($batchActionDto->getReferrerUrl());
    }

    public function publishPhotos(BatchActionDto $batchActionDto)
    {
        $publication = new Publication();

        // Dernière publication
        $lastPubs = $this->em->getRepository(Publication::class)->findBy([], ['date' => "desc"], 1);
        $lastPub = $lastPubs[0] ?? null;
        /** @var Publication $lastPub */
        if ($lastPub) {
            $date = (clone ($lastPub->getDate()))->modify("monday next week");
        }
        else {
            $date = (new \DateTime())->modify("monday next week");
        }
        $publication->setDate($date);

        foreach ($batchActionDto->getEntityIds() as $id) {
            $photo = $this->em->getRepository(Photo::class)->find($id);
            $publication->addPhoto($photo);
        }
        $this->em->persist($publication);
        $this->em->flush();

        $url = $this->adminUrlGenerator
            ->setController(PublicationCrudController::class)
            ->setAction(ACTION::EDIT)
            ->setEntityId($publication->getId())
            ->generateUrl();

        return $this->redirect($url);
    }

}
