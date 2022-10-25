<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateSortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {     
        $user = $options['user'];   

        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie', 
                'required' => true
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie', 
                'required' => true
            ])
            ->add('duree', TimeType::class, [
                'label' => 'Durée', 
                'required' => true
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label' => 'Date limite d\'inscription', 
                'required' => true
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' => 'Nombre maximum de personnes', 
                'required' => true
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description', 
                'required' => false
            ])
            /* ->add('organisateur', TextType::class, [
                        'data' => $user->getNom(),
                        'disabled' => true                        
            ]) */
            ->add('campus', EntityType::class, [
                'label' => 'Campus', 
                'class' => Campus::class, 'choice_label' => 'nom',
                'data' => $user->getCampus(),
                'disabled' => true  
            ])
            ->add('ville', EntityType::class, [
                'mapped' => false,
                'label' => 'Ville',
                'class' => Ville::class, 'choice_label' => 'nom',
                'placeholder' => 'Choisir une ville',
                'query_builder' => function (VilleRepository  $villeRepository) {
                    return $villeRepository->createQueryBuilder('v')->orderBy('v.nom', 'ASC');
                    },   
            ])
           /*  ->add('lieu', EntityType::class, [
                'label' => 'Lieu',
                'class' => Lieu::class, 'choice_label' => 'nom',
                'placeholder' => 'Choisir un lieu',
                'required' => true,
                'query_builder' => function (LieuRepository  $lieuRepository) {
                    return $lieuRepository->createQueryBuilder('l')->orderBy('l.nom', 'ASC');
                    },  
            ]) */
           /*  ->add('rue', TextType::class, [
                'mapped' => false,
                'label' => 'Rue',
                'disabled' => true  
            ])
            ->add('codepostal', TextType::class, [
                'mapped' => false,
                'label' => 'Code Postal',
                'disabled' => true  
            ])
            ->add('latitude', TextType::class, [
                'mapped' => false,
                'label' => 'Latitude',
                'required' => false
            ])
            ->add('longitude', TextType::class, [
                'mapped' => false,
                'label' => 'Longitude',
                'required' => false
            ]) */
            ;
            $formModifier = function (FormInterface $form, Ville $ville = null) {
                $lieux = null === $ville ? [] : $ville->getLieux();
  
                $form->add('lieu', EntityType::class, [
                    'class' => Lieu::class,
                    'placeholder' => '',
                    'choices' => $lieux,
                ]);
            };
            $builder->get('ville')->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) use ($formModifier) {
                    // It's important here to fetch $event->getForm()->getData(), as
                    // $event->getData() will get you the client data (that is, the ID)
                    $ville = $event->getForm()->getData();
    
                    // since we've added the listener to the child, we'll have to pass on
                    // the parent to the callback function!
                    $formModifier($event->getForm()->getParent(), $ville);
                }
            );
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'user' => null,
        ]);
    }
}
