<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app.security.login')]
    public function login(): Response
    {
        return $this->render('security/login.html.twig');
    }

    #[Route(path: '/logout', name: 'app.security.logout')]
    public function logout()
    {
    }

    #[Route(path: '/signup', name: 'app.signup')]
    public function register(): Response
    {
        return $this->render('security/registration.html.twig');
    }
}