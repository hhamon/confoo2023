<?php

namespace App\Controller;

use App\Appointment\AppointmentMailer;
use App\Entity\AgendaSlot;
use App\Entity\Appointment;
use App\Entity\User;
use App\Form\AppointmentType;
use App\Repository\AppointmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/appointments/{id}/book', name: 'app_book_appointment', methods: ['GET', 'POST'])]
final class BookAppointmentController extends AbstractController
{
    public function __construct(
        private readonly AppointmentRepository $appointmentRepository,
        private readonly AppointmentMailer $appointmentMailer,
    ) {
    }

    public function __invoke(Request $request, AgendaSlot $slot): Response
    {
        if (!$slot->isOpen()) {
            throw $this->createNotFoundException('Slot is not open for bookings.');
        }

        $user = $this->getUser();
        \assert($user instanceof User || $user === null);

        if ($user && $slot->isOwnedBy($user)) {
            throw $this->createAccessDeniedException('Current cannot book him\herself.');
        }

        $agenda = $slot->getAgenda();

        $appointment = new Appointment(
            $slot,
            (string) $user?->getFullName(),
            (string) $user?->getEmail(),
        );

        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->appointmentRepository->save($appointment, flush: true);
            $this->appointmentMailer->sendConfirmationEmails($appointment);

            return $this->redirectToRoute('app_display_agenda', ['slug' => $agenda->getSlug()]);
        }

        return $this->render('appointment/book_appointment.html.twig', [
            'appointment' => $appointment,
            'agenda_slot' => $slot,
            'agenda' => $agenda,
            'form' => $form->createView(),
        ]);
    }
}
