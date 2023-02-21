<?php

namespace App\Entity;

use App\Repository\AgendaSlotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: AgendaSlotRepository::class)]
class AgendaSlot
{
    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_BOOKED = 'booked';

    #[ORM\Id]
    #[ORM\Column(length: 36)]
    private string $id;

    #[ORM\Column]
    private \DateTimeImmutable $opensAt;

    #[ORM\Column]
    private \DateTimeImmutable $closesAt;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_OPEN;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Agenda $agenda;

    /**
     * @var Collection<int, Appointment>
     */
    #[ORM\OneToMany(mappedBy: 'slot', targetEntity: Appointment::class)]
    private Collection $appointments;

    public function __construct(
        Agenda $agenda,
        \DateTimeImmutable $opensAt,
        \DateTimeImmutable $closesAt,
        ?string $id = null,
    ) {
        if ($id) {
            $id = Uuid::fromString($id);
        }

        if ($opensAt->getTimezone()->getName() !== $opensAt->getTimezone()->getName()) {
            throw new \LogicException('The datetime range values are not on the same timezone.');
        }

        $this->id = $id ? (string) $id : Uuid::v4()->toRfc4122();
        $this->agenda = $agenda;
        $this->opensAt = $opensAt;
        $this->closesAt = $closesAt;
        $this->appointments = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return Uuid::fromString($this->id);
    }

    public function getOpensAt(): \DateTimeImmutable
    {
        return $this->opensAt;
    }

    public function getLocalOpensAt(): \DateTimeImmutable
    {
        $utcOpensAt = clone $this->opensAt;

        return $utcOpensAt->setTimezone(new \DateTimeZone($this->agenda->getTimezone()));
    }

    public function getClosesAt(): ?\DateTimeImmutable
    {
        return $this->closesAt;
    }

    public function getLocalClosesAt(): \DateTimeImmutable
    {
        $utcClosesAt = clone $this->closesAt;

        return $utcClosesAt->setTimezone(new \DateTimeZone($this->agenda->getTimezone()));
    }

    public function getTimezone(): string
    {
        return $this->agenda->getTimezone();
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAgenda(): Agenda
    {
        return $this->agenda;
    }

    /**
     * @return Collection<int, Appointment>
     */
    public function getAppointments(): Collection
    {
        return $this->appointments;
    }

    public function addAppointment(Appointment $appointment): self
    {
        if (!$this->equals($appointment->getSlot())) {
            throw new \LogicException('Appointment slot does match the current slot.');
        }

        if (!$this->appointments->contains($appointment)) {
            $this->appointments->add($appointment);
        }

        return $this;
    }

    public function equals(mixed $other): bool
    {
        return $other instanceof self && $this->getId()->equals($other->getId());
    }
}
