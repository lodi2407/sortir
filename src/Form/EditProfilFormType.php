<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditProfilFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, 
                    [
                        'label' => 'Nom', 
                        'required' => false
                    ]
                )
            ->add('prenom', TextType::class,
                    [
                        'label' => 'Prénom', 
                        'required' => false
                    ]
                )
            ->add('telephone', TextType::class, 
                    [
                        'label' => 'Téléphone', 
                        'required' => false
                    ]
                )
            ->add('email', EmailType::class, 
                    [
                        'label' => 'Email', 
                        'required' => true
                    ]
                )
                ->add('campus', EntityType::class, 
                    [
                        'label' => 'Campus', 
                        'class' => Campus::class, 'choice_label' => 'nom'
                    ]
                )
            ->add('photo', FileType::class, [
                        'label' => 'Photo', 
                        'required' => false
                    ]
                )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
