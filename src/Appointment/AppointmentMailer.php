<?php

declare(strict_types=1);

namespace App\Appointment;

use App\Entity\Appointment;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class AppointmentMailer
{
    public function __construct(
        private readonly MailerInterface $mailer,
    ) {
    }

    public function sendConfirmationEmails(Appointment $appointment): void
    {
        $this->sendAppointmentConfirmationToGuest($appointment);
        $this->sendAppointmentConfirmationToOwner($appointment);
    }

    private function sendAppointmentConfirmationToGuest(Appointment $appointment): void
    {
        $slot = $appointment->getSlot();
        $agenda = $appointment->getAgenda();

        $email = (new TemplatedEmail())
            ->subject(\sprintf('Your appointment with %s is confirmed.', $slot->getOwner()->getFullName()))
            ->to(new Address($appointment->getGuestEmail(), $appointment->getGuestName()))
            ->textTemplate('email/guest_appointment_confirmed.txt.twig')
            ->context([
                'appointment' => $appointment,
                'agenda_slot' => $slot,
                'agenda' => $agenda,
            ]);

        $this->mailer->send($email);
    }

    private function sendAppointmentConfirmationToOwner(Appointment $appointment): void
    {
        $slot = $appointment->getSlot();
        $agenda = $appointment->getAgenda();

        $email = (new TemplatedEmail())
            ->subject(\sprintf('You have a new appointment with %s.', $appointment->getGuestName()))
            ->to(new Address($slot->getOwner()->getEmail(), $slot->getOwner()->getFullName()))
            ->textTemplate('email/owner_appointment_confirmed.txt.twig')
            ->context([
                'appointment' => $appointment,
                'agenda_slot' => $slot,
                'agenda' => $agenda,
            ]);

        $this->mailer->send($email);
    }
}
