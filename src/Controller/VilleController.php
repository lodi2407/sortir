<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleFormType;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ville', name: 'app_ville')]
class VilleController extends AbstractController
{
    #[Route('/', name: '_list', methods: ['GET', 'POST'])]
    public function index(Request $request, VilleRepository $villeRepository): Response
    {
        $villes = $villeRepository->findBy([], ['nom' => 'ASC']);

        $ville = new Ville();

        // add ville
        $form = $this->createForm(VilleFormType::class, $ville);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {  

            $villeRepository->save($ville, true);
            $this->addFlash('success', 'Ville ajoutée !');
            return $this->redirectToRoute('app_ville_list', [], Response::HTTP_SEE_OTHER);
        }

        // edit ville
        /* $formModifVille = $this->createForm(VilleFormType::class, $ville);
        $formModifVille->handleRequest($request);

        if ($formModifVille->isSubmitted() && $formModifVille->isValid()) {
            $villeRepository->save($ville, true);

            return $this->redirectToRoute('app_ville_list', [], Response::HTTP_SEE_OTHER);
        } */

        return $this->render('ville/ville.html.twig', [
            'villes' => $villes,
            'createVilleForm' => $form->createView(),
         /*    'formModifVille' => $formModifVille->createView() */
        ]);
    }

/*     #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, VilleRepository $villeRepository): Response
    {
        $ville = new Ville();
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $villeRepository->save($ville, true);

            return $this->redirectToRoute('app_ville_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ville/new.html.twig', [
            'ville' => $ville,
            'form' => $form,
        ]);
    } */

/*     #[Route('/{id}', name: '_show', methods: ['GET'])]
    public function show(Ville $ville): Response
    {
        return $this->render('ville/show.html.twig', [
            'ville' => $ville,
        ]);
    } */

    #[Route('/edit/{id}', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ville $ville, VilleRepository $villeRepository): Response
    {
        $form = $this->createForm(VilleFormType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $villeRepository->save($ville, true);

            return $this->redirectToRoute('app_ville_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ville/edit.html.twig', [
            'ville' => $ville,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_delete', methods: ['POST', 'GET'])]
    public function delete(Request $request, Ville $ville, VilleRepository $villeRepository): Response
    {
        /* if ($this->isCsrfTokenValid('delete'.$ville->getId(), $request->request->get('_token'))) {
            $villeRepository->remove($ville, true);
        } */

        $villeRepository->remove($ville, true);
        $this->addFlash('success', 'Ville supprimée !');

        return $this->redirectToRoute('app_ville_list', [], Response::HTTP_SEE_OTHER);
    }
}
