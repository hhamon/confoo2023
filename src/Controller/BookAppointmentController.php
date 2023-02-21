<?php

namespace App\Controller;

use App\Entity\AgendaSlot;
use App\Entity\Appointment;
use App\Entity\User;
use App\Form\AppointmentType;
use App\Repository\AppointmentRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/appointments/{id}/book', name: 'app_book_appointment', methods: ['GET', 'POST'])]
final class BookAppointmentController extends AbstractController
{
    public function __construct(
        private readonly AppointmentRepository $appointmentRepository,
        private readonly MailerInterface $mailer,
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

            // Send email to guest
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

            // Send email to calendar's owner
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
