<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Form\LieuFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/lieu', name: 'app_lieu')]
class LieuController extends AbstractController
{
    #[Route('/create/{id}', name: '_create')]
    public function index(Request $request, Participant $user, EntityManagerInterface $entityManager): Response
    {
        $lieu = new Lieu();

        $form = $this->createForm(LieuFormType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success','Lieu ajouté.');
            return $this->redirectToRoute('app_sortie_create', ['id' => $user->getId()]);

        }
        return $this->render('lieu/create.html.twig', [
            'createLieuForm' => $form->createView(),
        ]);
    }
}
