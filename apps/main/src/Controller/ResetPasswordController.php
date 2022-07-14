<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{
    #[Route(path: '/forgot-password', name: 'app.reset_password.request')]
    public function forgotPassword(): Response
    {
        return new Response();
    }
}