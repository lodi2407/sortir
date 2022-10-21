<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\CreateSortieFormType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'app_sortie')]
class SortieController extends AbstractController
{
    #[Route('/', name: '_list')]
    public function listSorties(): Response
    {
        return $this->render('sortie/sortie.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }

    #[Route('/create/{id}', name: '_create')]
    public function createSortie(Participant $user, Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepository, LieuRepository $lieuRepository): Response
    {
        $sortie = new Sortie();
        $sortie->setOrganisateur($user);

        $etat = new Etat();
        $etat = $etatRepository->findOneById(1);
        $sortie->setEtat($etat);

        $form = $this->createForm(CreateSortieFormType::class, $sortie, ['user' => $user]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {  

            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success','Sortie créée.');
        }

        return $this->render('sortie/createSortie.html.twig', [
             'createSortieForm' => $form->createView(),
        ]);
    }
}
