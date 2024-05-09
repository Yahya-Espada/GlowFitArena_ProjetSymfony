<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Categorie;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType; // Import the FileType
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('libelle', TextType::class, [
            'constraints' => [
                new NotBlank(),
                new Length(['max' => 20]),
                new Type(['type' => 'string']),
            ],
        ])
        ->add('quantite', IntegerType::class, [
            'constraints' => [
                new NotBlank(),
                new Positive(),
            ],
        ])
        ->add('prix', IntegerType::class, [
            'constraints' => [
                new NotBlank(),
                new Positive(),
            ],
        ])
            ->add('imageFile', FileType::class, [ // Add the imageFile field
                'label' => 'Image du produit',
                'required' => false, // Make it not required since it's optional
                'mapped' => false, // Not mapped to any entity property
            ])
            ->add('photoProduit', HiddenType::class, [ // Add the hidden photoProduit field
                'mapped' => true, // This field is mapped to the photoProduit property of the entity
            ])
            ->add('idCategorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nomCategorie', // The property of Category entity to be shown in the dropdown
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.nomCategorie', 'ASC');
                },
            ])

        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
