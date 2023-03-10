<?php

namespace App\Entity;

use App\Repository\AppointmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContext;

#[ORM\Entity(repositoryClass: AppointmentRepository::class)]
class Appointment
{
    #[ORM\Id]
    #[ORM\Column(length: 36)]
    private string $id;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    #[ORM\JoinColumn(nullable: false)]
    private AgendaSlot $slot;

    #[ORM\Column(length: 50)]
    #[NotBlank(message: 'Guest name is required.')]
    #[Length(min: 2, max: 50)]
    private string $guestName;

    #[ORM\Column(length: 180)]
    #[NotBlank(message: 'Guest email is required.')]
    #[Email]
    private string $guestEmail;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Length(max: 2_000)]
    private ?string $message = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[Callback]
    public static function validateLegitGuestEmail(self $appointment, ExecutionContext $context): void
    {
        $guestAddress = $appointment->getGuestEmail();
        $ownerAddress = $appointment->getSlot()->getOwner()->getEmail();

        if ($guestAddress !== $ownerAddress) {
            return;
        }

        $context->buildViolation('This is a forbidden email address.')
            ->atPath('guestEmail')
            ->setInvalidValue($guestAddress)
            ->addViolation();
    }

    public function __construct(
        AgendaSlot $slot,
        string $guestName,
        string $guestEmail,
        ?string $message = null,
        ?string $id = null,
    ) {
        if ($id) {
            $id = Uuid::fromString($id);
        }

        $this->id = $id ? (string) $id : Uuid::v4()->toRfc4122();

        $this->slot = $slot;
        $this->slot->lock();

        $this->setGuestName($guestName);
        $this->setGuestEmail($guestEmail);
        $this->setMessage($message);
        $this->createdAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    public function getId(): Uuid
    {
        return Uuid::fromString($this->id);
    }

    public function getSlot(): AgendaSlot
    {
        return $this->slot;
    }

    public function getAgenda(): Agenda
    {
        return $this->slot->getAgenda();
    }

    public function getGuestName(): string
    {
        return $this->guestName;
    }

    public function setGuestName(string $guestName): self
    {
        $this->guestName = $guestName;

        return $this;
    }

    public function getGuestEmail(): string
    {
        return $this->guestEmail;
    }

    public function setGuestEmail(string $guestEmail): self
    {
        $this->guestEmail = \mb_strtolower($guestEmail);

        return $this;
    }

    public function getMessage(): string
    {
        return (string) $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
