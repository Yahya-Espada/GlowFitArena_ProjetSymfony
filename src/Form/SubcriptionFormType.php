<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\FileType;
class SubcriptionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('username', null, [
            'attr' => ['class' => 'form-control'],
            'constraints' => [
                new Length([
                    'min' => 4,
                    'minMessage' => 'Your usersname should be at least {{ limit }} characters long.',
                ]),
            ],
        ])
        ->add('email', null, [
            'attr' => ['class' => 'form-control'],
            'constraints' => [
                new Email(['message' => 'Please enter a valid email address.']),
            ],
        ])
        ->add('mot_de_passe', PasswordType::class, [
            'attr' => ['class' => 'form-control'],
            'constraints' => [
                new Length([
                    'min' => 6,
                    'minMessage' => 'Your password should be at least {{ limit }} characters long.',
                ]),
            ],
        ])
        ->add('image', FileType::class, [
            'label' => 'Chargez ici une photo',
            'required' => false,
            'mapped' => false,
            'attr' => ['class' => 'form-control-file'],
            'label_attr' => ['class' => 'form-label'],
        ])
        ->add('numero_de_telephone', null, [
            'attr' => ['class' => 'form-control'],
            'constraints' => [
                new Length([
                    'exactMessage' => 'The phone number must be exactly {{ limit }} digits.',
                    'min' => 8,
                    'max' => 8,
                ]),
                new Regex([
                    'pattern' => '/^\d+$/',
                    'message' => 'The phone number must be numeric.'
                ])
            ],
        ]);
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
