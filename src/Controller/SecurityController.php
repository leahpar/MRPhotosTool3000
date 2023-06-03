<?php

namespace App\Controller;

use App\Entity\Modele;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('front_shootings');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): never
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/reset-password', name: 'app_reset_password')]
    public function resetPassword(Request $request,
                                  EntityManagerInterface $em,
                                  UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $token = $request->query->get('token');
        if (!$token) {
            return $this->redirectToRoute('index');
        }

        $user = $em->getRepository(Modele::class)->findOneBy(['resetPasswordToken' => $token]);
        if ($user === null) {
            return $this->redirectToRoute('index');
        }

        if ($request->isMethod('POST')) {
            //$user->setPassword(rand()); // <== Pour déclencher le preUpdate, mais pas déclenché :/
            $password = $request->request->get('_password');
            $password = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($password);
            $user->setResetPasswordToken(null);
            $em->flush();
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', [
            'username' => $user->getUsername(),
        ]);
    }
}
