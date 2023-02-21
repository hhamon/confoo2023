<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'user_email_unique', columns: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_FREEMIUM_USER = 'ROLE_FREEMIUM_USER';
    public const ROLE_PREMIUM_USER = 'ROLE_PREMIUM_USER';

    #[ORM\Id]
    #[ORM\Column(length: 36)]
    private string $id;

    #[ORM\Column(length: 180)]
    private string $email;

    /**
     * @var string[]
     */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private string $password;

    #[ORM\Column]
    private string $fullName;

    /**
     * @var Collection<int, Agenda>
     */
    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Agenda::class)]
    private Collection $agendas;

    public function __construct(
        string $fullName,
        string $email,
        string $password,
        ?string $id = null,
    ) {
        if ($id) {
            $id = Uuid::fromString($id);
        }

        $this->id = $id ? (string) $id : Uuid::v4()->toRfc4122();
        $this->setFullName($fullName);
        $this->setEmail($email);
        $this->setPassword($password);
        $this->agendas = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return Uuid::fromString($this->id);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    private function setEmail(string $email): void
    {
        $this->email = \mb_strtolower($email);
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return \array_unique($roles);
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Agenda>
     */
    public function getAgendas(): Collection
    {
        return $this->agendas;
    }

    public function addAgenda(Agenda $agenda): self
    {
        if (!$this->equals($agenda->getOwner())) {
            throw new \LogicException('Agenda is not owned by the current user.');
        }

        if (!$this->agendas->contains($agenda)) {
            $this->agendas->add($agenda);
        }

        return $this;
    }

    public function removeAgenda(Agenda $agenda): self
    {
        if (!$this->equals($agenda->getOwner())) {
            throw new \LogicException('Agenda is not owned by the current user.');
        }

        if ($this->agendas->contains($agenda)) {
            $this->agendas->removeElement($agenda);
        }

        return $this;
    }

    public function equals(mixed $other): bool
    {
        return $other instanceof self && $this->getId()->equals($other->getId());
    }
}
