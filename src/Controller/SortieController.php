<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FiltersSorties;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\CreateSortieFormType;
use App\Form\FiltersSortiesFormType;
use App\Form\LieuFormType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'app_sortie')]
class SortieController extends AbstractController
{
    #[Route('/accueil', name: '_list', methods: ['GET', 'POST'])]
    public function listSorties(Request $request, SortieRepository $sortieRepository): Response
    {
        $filters = new FiltersSorties();
        $user = $this->getUser();

        $form = $this->createForm(FiltersSortiesFormType::class, $filters);
        $form->handleRequest($request);

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $sortieRepository->findFilteredSorties($filters, $user, $offset);

        if ($form->isSubmitted() && $form->isValid()) {  
            $paginator = $sortieRepository->findFilteredSorties($filters, $user, $offset);
        } 

        return $this->render('sortie/sortie.html.twig',[
            'sorties' => $paginator,
            'previous' => $offset - SortieRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + SortieRepository::PAGINATOR_PER_PAGE),
            'filtersForm' => $form->createView()
        ]);
    }

    #[Route('/create/{id}', name: '_create', methods: ['GET', 'POST'])]
    public function new(Participant $user, Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepository): Response
    {
        $sortie = new Sortie();
        $sortie->setOrganisateur($user);
        $sortie->setCampus($user->getCampus());
        $sortie->addParticipant($user);

        $etat = new Etat();
        $etat = $etatRepository->findOneById(1);
        $sortie->setEtat($etat);

        $form = $this->createForm(CreateSortieFormType::class, $sortie, ['user' => $user]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {  

            if ($sortie->getLieu()) {
                $entityManager->persist($sortie);
                $entityManager->flush();
    
                $this->addFlash('success','Sortie créée.');
                return $this->redirectToRoute('app_sortie_list');
            } else {
                $this->addFlash('warning','Sélectionnez un lieu');
            }

        }

        // ajouter un lieu
        $lieu = new Lieu();

        $formLieu = $this->createForm(LieuFormType::class, $lieu);
        $formLieu->handleRequest($request);

        if ($formLieu->isSubmitted() && $formLieu->isValid()) {  

            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success','Lieu ajouté.');
            
        }

        return $this->render('sortie/createSortie.html.twig', [
             'createSortieForm' => $form->createView(),
             'createLieuForm' => $formLieu->createView(),
        ]);
    }

    #[Route('/{id}', name: '_show', methods: ['GET'])]
    public function show(Sortie $sortie): Response
    {
        $participants = $sortie->getParticipant();

        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
            'participants' => $participants
        ]);
    }

    #[Route('/edit/{id}', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sortie $sortie, SortieRepository $sortieRepository, LieuRepository $lieuRepository): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(CreateSortieFormType::class, $sortie, ['user' => $user]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortieRepository->save($sortie, true);

            return $this->redirectToRoute('app_sortie_list', [], Response::HTTP_SEE_OTHER);
        }

        // Modifier lieu
        $lieuSortie = $sortie->getLieu();
        $formModifLieu = $this->createForm(LieuFormType::class, $lieuSortie);
        $formModifLieu->handleRequest($request);

        if ($formModifLieu->isSubmitted() && $formModifLieu->isValid()) {  

            $lieuRepository->save($lieuSortie, true);

            $this->addFlash('success','Lieu ajouté.');
            
        }

        // Créer un nouveau lieu
        $lieu = new Lieu;
        $formLieu = $this->createForm(LieuFormType::class, $lieu);
        $formLieu->handleRequest($request);

        if ($formLieu->isSubmitted() && $formLieu->isValid()) {  

            $lieuRepository->save($lieu, true);

            $this->addFlash('success','Lieu ajouté.');
            
        }

        return $this->renderForm('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'lieu' => $lieuSortie,
            'modifierSortieForm' => $form,
            'modifierLieuForm' => $form,
            'createLieuForm' => $formLieu
        ]);
    }

    #[Route('/{id}', name: '_delete', methods: ['POST'])]
    public function delete(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $sortieRepository->remove($sortie, true);
        }

        return $this->redirectToRoute('app_sortie_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/addParticipant/{id}', name: '_addParticipant', methods: ['GET'])]
    public function addParticipant(Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        $user= $this->getUser();

        $sortie->addParticipant($user);
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Inscription réussie !');
        return $this->redirectToRoute('app_sortie_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/removeParticipant/{id}', name: '_removeParticipant', methods: ['GET'])]
    public function unFollow(Sortie $sortie,EntityManagerInterface $entityManager): Response
    {
        $participant= $this->getUser();

        $sortie->removeParticipant($participant);

        $entityManager->persist($sortie);
        $entityManager->flush();

        $this->addFlash('success', 'Désinscription réussie !');
        return $this->redirectToRoute('app_sortie_list', [], Response::HTTP_SEE_OTHER);
    }
}
