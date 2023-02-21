<?php

namespace App\Controller;

use App\Entity\AgendaSlot;
use App\Entity\Appointment;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/appointments/{id}/book', name: 'app_book_appointment', methods: ['GET', 'POST'])]
final class BookAppointmentController extends AbstractController
{
    public function __invoke(AgendaSlot $slot): Response
    {
        if (!$slot->isOpen()) {
            throw $this->createNotFoundException('Slot is not open for bookings.');
        }

        $user = $this->getUser();
        \assert($user instanceof User || $user === null);

        if ($user && $slot->isOwnedBy($user)) {
            throw $this->createAccessDeniedException('Current cannot book him\herself.');
        }

        $appointment = new Appointment(
            $slot,
            (string) $user?->getFullName(),
            (string) $user?->getEmail(),
        );

        // TODO: add form + handling

        return $this->render('appointment/book_appointment.html.twig', [
            'appointment' => $appointment,
            'agenda_slot' => $slot,
            'agenda' => $slot->getAgenda(),
        ]);
    }
}
