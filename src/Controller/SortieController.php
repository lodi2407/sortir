<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\CreateSortieFormType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'app_sortie')]
class SortieController extends AbstractController
{
    #[Route('/accueil/{id}', name: '_list')]
    public function listSorties(Request $request, Participant $user, SortieRepository $sortieRepository, EntityManagerInterface $entityManager): Response
    {
        $sorties = $sortieRepository->findBy([], ['dateHeureDebut' => 'ASC']);

        return $this->render('sortie/sortie.html.twig', compact('sorties'));
    }

    #[Route('/create/{id}', name: '_create')]
    public function createSortie(Participant $user, Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepository): Response
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
