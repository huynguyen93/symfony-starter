<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    const ROLE_USER = 'ROLE_USER';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $displayName = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $avatarUrl = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $password = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $verifiedEmailAt = null;

    private bool $hasBeenWelcomed = true;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getRoles(): array
    {
        return [self::ROLE_USER];
    }

    public function eraseCredentials()
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getAvatarUrl(): string
    {
        if (is_null($this->avatarUrl)) {
            return 'https://ui-avatars.com/api/?name=' . $this->displayName;
        }

        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl)
    {
        $this->avatarUrl = $avatarUrl;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getVerifiedEmailAt(): ?DateTimeImmutable
    {
        return $this->verifiedEmailAt;
    }

    public function setVerifiedEmailAt(?DateTimeImmutable $verifiedEmailAt): self
    {
        $this->verifiedEmailAt = $verifiedEmailAt;

        return $this;
    }

    public function hasBeenWelcomed(): bool
    {
        return $this->hasBeenWelcomed;
    }

    public function setHasBeenWelcomed(bool $welcomed): void
    {
        $this->hasBeenWelcomed = $welcomed;
    }
}
