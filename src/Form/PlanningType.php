<?php

namespace App\Form;

use App\Entity\Planning;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PlanningType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Titre'
            ])
            ->add('nbseance', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Nombre de séances'
            ])
            ->add('prix', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Prix'
            ])
            ->add('iduser', ChoiceType::class, [
                'choices' => $options['users'], // assuming you pass users as options
                'attr' => ['class' => 'form-control'],
                'label' => 'Utilisateur'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Planning::class,
            'users' => null, // Add this line to accept 'users' as an option
        ]);
    }
}
