<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom', 
                'required' => true
            ])
            ->add('rue', TextType::class, [
                'label' => 'Rue', 
                'required' => true
            ])
            ->add('latitude', NumberType::class, [
                'label' => 'Latitude', 
                'required' => false
            ])
            ->add('longitude', NumberType::class, [
                'label' => 'Latitude', 
                'required' => false
                ])
            ->add('ville', EntityType::class, [
                'label' => 'Ville', 
                'class' => Ville::class, 'choice_label' => 'nom',
                'query_builder' => function (VilleRepository  $villeRepository) {
                    return $villeRepository->createQueryBuilder('v')->orderBy('v.nom', 'ASC');
                    },  
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
