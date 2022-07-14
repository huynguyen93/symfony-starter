<?php

namespace App\Security\OauthClient;

use App\Entity\UserOauth;
use Symfony\Component\HttpFoundation\Request;

interface OauthClientInterface
{
    public function getUserOauth(Request $request): UserOauth;
}
