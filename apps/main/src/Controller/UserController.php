<?php

namespace App\Controller;

use App\Security\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(private Security $security)
    {
    }

    #[Route(path: '/my-profile', name: 'app.user.profile')]
    public function profile(): Response
    {
        $user = $this->security->getUser();

        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route(path: '/user/change-password', name: 'app.user.change_password')]
    public function changePassword(): Response
    {
        return $this->render('user/change_password.html.twig');
    }
}