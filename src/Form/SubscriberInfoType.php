<?php

namespace App\Form;

use App\Entity\SubscriberInfo;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriberInfoType extends AbstractType
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('height')
            ->add('weight')
            ->add('age')
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Homme' => 'male',
                    'Femme' => 'female',
                ],
            ])
            ->add('goals')
            ->add('activityLevel' , ChoiceType::class, [
                'choices' => [
                    'Sédentaire' => 'sedentary',
                    'Modérément actif' => 'moderately active',
                    'Actif' => 'active',
                    'Très actif' => 'very active',
                ],
            ])
            ->add('dietaryRestrictions')
            ->add('foodPreferences')
          //  ->add('createdAt')
        //    ->add('updatedAt')
        ;
        $subscriberInfo = $builder->getData();
        if (!$this->entityManager->contains($subscriberInfo)) {
            $builder
              ->add('user', ChoiceType::class, [
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
            'data_class' => SubscriberInfo::class,
            'user_choices' => [],
        ]);
    }
}
