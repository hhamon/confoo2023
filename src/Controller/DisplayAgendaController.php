<?php

namespace App\Controller;

use App\Entity\Agenda;
use App\Repository\AgendaRepository;
use App\Repository\AgendaSlotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/agenda/{slug}', name: 'app_display_agenda', methods: ['GET'])]
class DisplayAgendaController extends AbstractController
{
    public function __construct(
        private readonly AgendaRepository $agendaRepository,
        private readonly AgendaSlotRepository $agendaSlotRepository,
    ) {
    }

    public function __invoke(string $slug): Response
    {
        $agenda = $this->agendaRepository->findOneBy(['slug' => $slug, 'isEnabled' => true]);

        if (!$agenda instanceof Agenda) {
            throw $this->createNotFoundException('Agenda does not exist!');
        }

        return $this->render('agenda/agenda.html.twig', [
            'agenda' => $agenda,
            'upcoming_slots' => $this->agendaSlotRepository->findUpcomingSlots($agenda),
        ]);
    }
}
