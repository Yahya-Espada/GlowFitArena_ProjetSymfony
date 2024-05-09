<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;

class ResetPasswordEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', null, [
            'attr' => ['class' => 'form-control','validate'=>'no-validate'],
            'required' => false, // Set required to false 
        ])
        ->add('phoneNumber', null, [
            'attr' => ['class' => 'form-control','validate'=>'no-validate'],
            'required' => false, // Set required to false

            
        ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
