<?php

namespace App\Form;

use App\Entity\DietaryProgramsCopy;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DietaryProgramsCopyType extends AbstractType
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('startDate')
            ->add('endDate')
            ->add('calorieGoal')
            ->add('macroRatioCarbs')
            ->add('macroRatioProtein')
            ->add('macroRatioFat')
            ->add('mealTypes')
            ->add('notes')
         //   ->add('createdAt')
         //   ->add('updatedAt')
        ;
        // lina n7otou les champs coach w subscriber f cas l'objet DietaryProgramsCopy mch mawjoud f la base de données
        $dietaryProgramsCopy = $builder->getData();
        if (!$this->entityManager->contains($dietaryProgramsCopy)) {
            $builder
                ->add('coach', ChoiceType::class, [
                    'choices' => $options['coach_choices'],
                    'choice_label' => function(User $user) {
                        return $user->getUsername();
                    },
                    'choice_value' => function(User $user = null) {
                        return $user ? $user->getId() : '';
                    },
                ])
                ->add('subscriber', ChoiceType::class, [
                    'choices' => $options['user_choices'],
                    'choice_label' => function(User $user) {
                        return $user->getUsername();
                    },
                    'choice_value' => function(User $user = null) {
                        return $user ? $user->getId() : '';
                    },
                ]);
        }
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DietaryProgramsCopy::class,
            'user_choices' => [], // lezim ykoun array mn les objets User wla null
            'coach_choices' => [], // must be an array of User objects or null
        ]);
    }
}
