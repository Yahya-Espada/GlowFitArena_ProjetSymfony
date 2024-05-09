<?php

namespace App\Form;

use App\Entity\Seance;
use App\Entity\Planning;
use App\Entity\User;
use App\Repository\PlanningRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeanceType extends AbstractType
{
    private $entityManager;
    private $planningRepository;

    public function __construct(EntityManagerInterface $entityManager, PlanningRepository $planningRepository)
    {
        $this->entityManager = $entityManager;
        $this->planningRepository = $planningRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('heuredebut')
            ->add('heurefin')
            ->add('date')
            ->add('prix')
            ->add('typeseance')
            ->add('idPlanning', EntityType::class, [
                'class' => Planning::class,
                'choice_label' => 'titre',
                'choices' => $this->planningRepository->findAll(),
                'label' => ' titre Planning',
            ])
            ->add('iduser', EntityType::class, [
                'class' => 'App\Entity\User',
                'choice_label' => 'username', // Assurez-vous que 'nom' est bien le nom de la propriété contenant le nom de l'utilisateur
                'choices' => $this->entityManager->getRepository(User::class)->findBy(['role' => 'coach']),
                'label' => 'coach',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Seance::class,
        ]);
    }
}
