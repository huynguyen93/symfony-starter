<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Security as SymfonySecurity;

class Security
{
    public function __construct(
        private SymfonySecurity $security,
    ) {
    }

    public function getUser(): ?User
    {
        $user = $this->security->getUser();

        if ($user instanceof User) {
            return $user;
        }

        return null;
    }
}
