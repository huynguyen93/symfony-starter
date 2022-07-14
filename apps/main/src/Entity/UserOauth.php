<?php

namespace App\Entity;

use App\Repository\UserOauthRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserOauthRepository::class)]
#[ORM\UniqueConstraint(name: 'unique_provider_idx', columns: ['provider', 'origin_id'])]
class UserOauth
{
    const PROVIDER_FACEBOOK = 'facebook';
    const PROVIDER_GOOGLE = 'google';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(type: 'string')]
    private string $provider;

    #[ORM\Column(type: 'string')]
    private string $originId;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $avatarUrl = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }

    public function getOriginId(): ?string
    {
        return $this->originId;
    }

    public function setOriginId(string $originId): void
    {
        $this->originId = $originId;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl): void
    {
        $this->avatarUrl = $avatarUrl;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
