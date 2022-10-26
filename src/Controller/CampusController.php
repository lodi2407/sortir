<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusFormType;
use App\Repository\CampusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/campus', name:'app_campus')]
class CampusController extends AbstractController
{
    #[Route('/', name: '_list', methods: ['GET', 'POST'])]
    public function index(Request $request, CampusRepository $campusRepository): Response
    {
        $campuses = $campusRepository->findBy([], ['nom' => 'ASC']);

        $campus = new Campus();

        $form = $this->createForm(CampusFormType::class, $campus);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {  

            $campusRepository->save($campus, true);
            $this->addFlash('success', 'Campus ajoutée !');
            return $this->redirectToRoute('app_campus_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('campus/campus.html.twig', [
            'campuses' => $campuses,
            'createCampusForm' => $form->createView(),
        ]);
    }

/*     #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CampusRepository $campusRepository): Response
    {
        $campus = new Campus();
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campusRepository->save($campus, true);

            return $this->redirectToRoute('app_campus_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('campus/new.html.twig', [
            'campus' => $campus,
            'form' => $form,
        ]);
    } */

/*     #[Route('/{id}', name: '_show', methods: ['GET'])]
    public function show(Campus $campus): Response
    {
        return $this->render('campus/show.html.twig', [
            'campus' => $campus,
        ]);
    } */

    #[Route('/edit/{id}', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Campus $campus, CampusRepository $campusRepository): Response
    {
        $form = $this->createForm(CampusFormType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campusRepository->save($campus, true);

            return $this->redirectToRoute('app_campus_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('campus/edit.html.twig', [
            'campus' => $campus,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_delete', methods: ['POST', 'GET'])]
    public function delete(Request $request, Campus $campus, CampusRepository $campusRepository): Response
    {
        /* if ($this->isCsrfTokenValid('delete'.$campus->getId(), $request->request->get('_token'))) {
            $campusRepository->remove($campus, true);
        } */

        $campusRepository->remove($campus, true);
        $this->addFlash('success', 'Campus supprimé !');

        return $this->redirectToRoute('app_campus_list', [], Response::HTTP_SEE_OTHER);
    }
}
