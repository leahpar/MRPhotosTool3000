<?php

namespace App\Controller;

use App\Entity\Modele;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field;

class ModeleCrudController extends AbstractCrudController
{

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

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
            ->setPaginatorPageSize(50)
            ->setDefaultSort(['nom' => 'ASC'])
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $action = Action::new('resetPassword', 'Reset mot de passe')
            ->linkToCrudAction('resetPassword')
            ->setIcon('fa fa-key')
            ;
        return $actions
            ->add(Crud::PAGE_INDEX, $action);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            'nom',
            'pseudo',
            'instagram',
            Field\DateField::new('dateDernierShooting')->onlyOnIndex(),
            'aSuivre',
            'dernierContact',
            'prochainContact',

            Field\FormField::addPanel("Connexion")
                ->onlyOnForms(),
            Field\TextField::new('username')
                ->setLabel("Login")
                ->onlyOnForms(),
            Field\TextField::new('plainPassword')
                ->setLabel("Nouveau mot de passe")
                ->onlyOnForms(),
        ];
    }

    public function resetPassword(AdminContext $context)
    {
        /** @var Modele $user */
        $user = $context->getEntity()->getInstance();
        $user->setResetPasswordToken(bin2hex(random_bytes(32)));
        $this->em->flush();

        return $this->render('admin/reset_password.html.twig', [
            'user' => $user,
        ]);

    }

}

