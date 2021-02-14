<?php

namespace App\Controller;

use App\Entity\Galerie;
use App\Entity\Modele;
use App\Entity\Photo;
use App\Entity\Publication;
use App\Entity\Shooting;
use App\Entity\Tag;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractDashboardController
{

    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('css/admin.css');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            // the name visible to end users
            ->setTitle('MRPhotosTool')
            // you can include HTML contents too (e.g. to link to an image)
            //->setTitle('<img src="..."> ACME <span class="text-small">Corp.</span>')

            // the path defined in this method is passed to the Twig asset() function
            ->setFaviconPath('favicon.png')

            // set this option if you prefer the page content to span the entire
            // browser width, instead of the default design which sets a max width
            //->renderContentMaximized()

            // set this option if you prefer the sidebar (which contains the main menu)
            // to be displayed as a narrow column instead of the default expanded design
            //->renderSidebarMinimized()

            // by default, all backend URLs include a signature hash. If a user changes any
            // query parameter (to "hack" the backend) the signature won't match and EasyAdmin
            // triggers an error. If this causes any issue in your backend, call this method
            // to disable this feature and remove all URL signature checks
            //->disableUrlSignatures()
        ;
    }

    public function configureMenuItems(): iterable
    {
        return [
            //MenuItem::linktoDashboard('Dashboard', 'fa fa-home'),

            MenuItem::linkToCrud('Modèles', 'fas fa-user', Modele::class),
            MenuItem::linkToCrud('Shootings', 'fas fa-camera', Shooting::class),
            MenuItem::linkToCrud('Tags', 'fas fa-tags', Tag::class),
            MenuItem::linkToCrud('Photos', 'fas fa-images', Photo::class),
            MenuItem::linkToCrud('Galeries', 'fas fa-folder-open', Galerie::class),
            MenuItem::linkToCrud('Publications', 'fas fa-bullhorn', Publication::class),
        ];
    }

    /**
     * @Route("/admin")
     */
    public function index(): Response
    {
        // redirect to some CRUD controller
        $routeBuilder = $this->get(AdminUrlGenerator::class);
        return $this->redirect($routeBuilder->setController(PhotoCrudController::class)->generateUrl());

        // you can also redirect to different pages depending on the current user
        //if ('jane' === $this->getUser()->getUsername()) {
        //    return $this->redirect('...');
        //}

        // you can also render some template to display a proper Dashboard
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //return $this->render('some/path/my-dashboard.html.twig');
    }
}