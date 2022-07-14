<?php

namespace App\Repository;

use App\Entity\UserLanguage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserLanguage>
 *
 * @method UserLanguage|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserLanguage|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserLanguage[]    findAll()
 * @method UserLanguage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserLanguageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserLanguage::class);
    }
}
