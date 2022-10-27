<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\EditPasswordType;
use App\Form\EditProfilFormType;
use App\Repository\ParticipantRepository;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route('/profil', name: 'app_profil')]
class ProfilController extends AbstractController
{
    #[Route('/edit/{id}', name: '_edit')]
    public function editProfile(Participant $user, Request $request, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EditProfilFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /*  $photo = $form->get('photo')->getData();
            if ($photo) {
                $photo->move($photo, '/img');
            } */
           
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success','Profil modifié.');

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        // edit password
        $formPassword = $this->createForm(EditPasswordType::class);
        $formPassword->handleRequest($request);
        
        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            // encode the plain password
            if ($formPassword->get('plainPassword')->getData() == $formPassword->get('confirmPassword')->getData()) {
               $user->setPassword(
                   $userPasswordHasher->hashPassword(
                       $user,
                       $formPassword->get('plainPassword')->getData()
                   )
               );
               
               $entityManager->persist($user);
               $entityManager->flush();

               $this->addFlash('success','Le mot de passe a été modifié.');
           } else {
               $this->addFlash('danger','Les mots de passe renseignés sont différents.');
           }
       }

        return $this->render('profil/profil.html.twig', [
            'editProfilForm' =>  $form->createView(),
            'editPasswordForm' =>  $formPassword->createView(),
        ]);
    }

    #[Route('/show/{id}', name: '_show')]
    public function showProfile(Participant $user, ParticipantRepository $participantRepository): Response
    {
        $user = $participantRepository->find($user->getId());

        return $this->render('profil/show.html.twig', [
            'user' => $user
        ]);
    }

/*     #[Route('/editPassword/{id}', name: '_editPassword')]
    public function editPassword(Participant $user, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager) {

        $form = $this->createForm(EditPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             // encode the plain password
             if ($form->get('plainPassword')->getData() == $form->get('confirmPassword')->getData()) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success','Le mot de passe a été modifié.');
            } else {
                $this->addFlash('danger','Les mots de passe renseignés sont différents.');
            }
        }

        return $this->render('profil/editPassword.html.twig', [
            'editPasswordForm' =>  $form->createView(),
        ]);
    } */
}
