<?php

namespace App\Repository;

use App\Entity\UserOauth;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserOauth|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserOauth|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserOauth[]    findAll()
 * @method UserOauth[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserOauthRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserOauth::class);
    }
    
    protected string $class = UserOauth::class;

    public function findOneByProvider(string $provider, string $originId): ?UserOauth
    {
        return $this->findOneBy([
            'provider' => $provider,
            'originId' => $originId,
        ]);
    }

    public function findByEmail(string $email): array
    {
        return $this->findBy([
            'email' => $email,
        ]);
    }

    public function findOneByProviderAndEmail(string $provider, string $email): ?UserOauth
    {
        return $this->findOneBy([
            'provider' => $provider,
            'email' => $email,
        ]);
    }

    public function findOneByProviderAndOriginId(string $provider, string $originId): ?UserOauth
    {
        return $this->findOneBy([
            'provider' => $provider,
            'originId' => $originId,
        ]);
    }

    public function findByUser(User $user): array
    {
        return $this->findBy([
            'user' => $user,
        ]);
    }

    public function findOneByUserAndProvider(User $user, string $provider): ?UserOauth
    {
        return $this->findOneBy([
            'user' => $user,
            'provider' => $provider,
        ]);
    }
}
