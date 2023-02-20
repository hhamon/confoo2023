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

    public function __construct(string $name, ?string $id = null)
    {
        $slugger = new AsciiSlugger('en');

        if ($id) {
            $id = Uuid::fromString($id);
        }

        $this->id = $id ? (string) $id : Uuid::v4()->toRfc4122();
        $this->setName($name);
        $this->setSlug((string) $slugger->slug($name));
    }

    public function getId(): Uuid
    {
        return Uuid::fromString($this->id);
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
}
