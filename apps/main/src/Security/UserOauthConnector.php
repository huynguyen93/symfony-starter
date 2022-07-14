<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\UserOauth;
use App\Exception\OauthUserAlreadyAssignedException;
use App\Exception\OauthUserMissingEmailException;
use App\Repository\UserOauthRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserOauthConnector
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserOauthRepository $userOauthRepository
    ) {
    }

    /**
     * @throws OauthUserAlreadyAssignedException
     * @throws OauthUserMissingEmailException
     */
    public function connect(UserOauth $userOauth, ?User $loggedInUser): User
    {
        $existingUserOauth = $this->userOauthRepository->findOneByProviderAndOriginId(
            $userOauth->getProvider(),
            $userOauth->getOriginId()
        );

        if ($existingUserOauth) {
            $userOauth = $existingUserOauth;
        }

        $user = $userOauth->getUser();

        if ($loggedInUser) { // connect current user with another social account
            if ($user && $loggedInUser !== $user) {
                throw new OauthUserAlreadyAssignedException();
            }

            if (!$user) {
                $user = $loggedInUser;
            }
        }

        if (!$userOauth->getEmail()) {
            throw new OauthUserMissingEmailException();
        }

        if (!$user) {
            $user = $this->userRepository->findOneByEmail($userOauth->getEmail());
        }

        if (!$user) {
            $user = new User();
            $user->setEmail($userOauth->getEmail());
            $user->setDisplayName($userOauth->getName());
            $user->setHasBeenWelcomed(false);

            $this->entityManager->persist($user);
        }

        $userOauth->setUser($user);

        if ($userOauth->getAvatarUrl()) {
            $user->setAvatarUrl($userOauth->getAvatarUrl());
        }

        if (!$existingUserOauth) {
            $this->entityManager->persist($userOauth);
        }

        $this->entityManager->flush();

        return $user;
    }
}
