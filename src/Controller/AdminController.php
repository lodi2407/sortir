<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'app_admin')]
class AdminController extends AbstractController
{
     #[Route('/dashboard', name: '_dashboard')]
    public function index(Request $request, ParticipantRepository $participantRepository): Response
    {
        $participantsActifs = $participantRepository->findActiveParticipants();
        $participantsInactifs = $participantRepository->findInactiveParticipants();
      
        return $this->render('admin/index.html.twig', [
            'participantsActifs' => $participantsActifs,
            'participantsInactifs' => $participantsInactifs,
        ]);
    } 
}
