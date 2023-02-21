<?php

namespace App\Controller;

use App\Entity\Agenda;
use App\Repository\AgendaRepository;
use App\Repository\AgendaSlotRepository;
use App\Security\Voter\AgendaVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/agendas/{slug}/manage', name: 'app_manage_agenda', methods: ['GET', 'POST'])]
class ManageAgendaController extends AbstractController
{
    public function __construct(
        private readonly AgendaRepository $agendaRepository,
        private readonly AgendaSlotRepository $agendaSlotRepository,
    ) {
    }

    public function __invoke(string $slug): Response
    {
        $agenda = $this->agendaRepository->findBySlug($slug);

        if (!$agenda instanceof Agenda) {
            throw $this->createNotFoundException('Agenda does not exist!');
        }

        $this->denyAccessUnlessGranted(AgendaVoter::EDIT, $agenda);

        return $this->render('agenda/agenda.html.twig', [
            'agenda' => $agenda,
            'upcoming_slots' => $this->agendaSlotRepository->findUpcomingSlots($agenda),
        ]);
    }
}
