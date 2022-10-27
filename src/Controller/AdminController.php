<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'app_admin')]
class AdminController extends AbstractController
{
    #[Route('/dashboard', name: '_dashboard')]
    public function index(ParticipantRepository $participantRepository): Response
    {
        $participantsActifs = $participantRepository->findActiveParticipants();
        $participantsInactifs = $participantRepository->findInactiveParticipants();
      
        return $this->render('admin/index.html.twig', [
            'participantsActifs' => $participantsActifs,
            'participantsInactifs' => $participantsInactifs,
        ]);
    } 

    #[Route('/delete/{id}', name: '_delUser')]
    public function delUser(Participant $user, ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($user);
        $participantRepository->remove($participant, true);

        $this->addFlash('success', 'Utilisateur supprimé !');

        return $this->redirectToRoute('app_admin_dashboard', [], Response::HTTP_SEE_OTHER);
    } 

    #[Route('/disableUser/{id}', name: '_disableUser')]
    public function disableUser(Participant $user, ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($user);
        $participant->setActif(0);
        $participantRepository->save($participant, true);

        $this->addFlash('success', 'Utilisateur désactivé !');

        return $this->redirectToRoute('app_admin_dashboard', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/activerUtilisateur/{id}', name: '_enableUser')]
    public function activerUtilisateur(Participant $user, ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($user);
        $participant->setActif(1);
        $participantRepository->save($participant, true);

        $this->addFlash('success', 'Utilisateur Activé !');

        return $this->redirectToRoute('app_admin_dashboard', [], Response::HTTP_SEE_OTHER);
    }
}
