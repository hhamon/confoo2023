<?php

namespace App\Entity;

use App\Repository\AgendaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: AgendaRepository::class)]
#[ORM\UniqueConstraint(name: 'agenda_slug_unique', columns: ['slug'])]
class Agenda
{
    #[ORM\Id]
    #[ORM\Column(length: 36)]
    private string $id;

    #[ORM\Column(length: 100)]
    private string $name;

    #[ORM\Column(length: 120)]
    private string $slug;

    #[ORM\Column]
    private bool $isEnabled = false;

    #[ORM\ManyToOne(inversedBy: 'agendas')]
    #[ORM\JoinColumn(nullable: false)]
    private User $owner;

    public function __construct(
        string $name,
        User $owner,
        ?string $id = null,
    ) {
        $slugger = new AsciiSlugger('en');

        if ($id) {
            $id = Uuid::fromString($id);
        }

        $this->id = $id ? (string) $id : Uuid::v4()->toRfc4122();
        $this->setOwner($owner);
        $this->setName($name);
        $this->setSlug((string) $slugger->slug($name)->lower());
    }

    public function getId(): Uuid
    {
        return Uuid::fromString($this->id);
    }

    public function getTimezone(): string
    {
        return 'America/Montreal';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    private function setOwner(User $owner): void
    {
        $this->owner = $owner;
        $this->owner->addAgenda($this);
    }
}
