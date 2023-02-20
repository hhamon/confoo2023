<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    #[Route('/hello/{name}', name: 'app_hello')]
    public function index(Request $request, string $name): Response
    {
        $firstName = $request->query->get('firstName');
        $lastName = $request->query->get('lastName');

        return $this->render('hello/agenda.html.twig', [
            'first_name' => $name,
            'last_name' => $lastName,
        ]);
    }
}
