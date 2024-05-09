<?php

namespace App\Controller;

use App\Entity\Planning;
use App\Entity\User;

use App\Form\PlanningType;
use App\Repository\PlanningRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;



#[Route('/planning')]
class PlanningController extends AbstractController
{


    #[Route('/', name: 'app_planning_index', methods: ['GET'])]
    public function index(PlanningRepository $planningRepository): Response
    {
        return $this->render('planning/index.html.twig', [
            'plannings' => $planningRepository->findAll(),
        ]);
    }
    #[Route('/new', name: 'app_planning_new')]

    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $planning = new Planning();

        // Récupérer les utilisateurs ayant le rôle de coach
        $coaches = $entityManager->getRepository(User::class)->findBy(['role' => 'coach']);

        $form = $this->createForm(PlanningType::class, $planning);
        // Modifier les options du formulaire pour inclure la liste des coaches
        $form->add('iduser', ChoiceType::class, [
            'choices' => $this->getCoachChoices($coaches),
            'choice_label' => function ($user) {
                return $user->getUsername();
            },
            'label' => 'nom user',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($planning);
            $entityManager->flush();

            return $this->redirectToRoute('app_planning_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('planning/new.html.twig', [
            'planning' => $planning,
            'form' => $form->createView(),
        ]);
    }

    private function getCoachChoices(array $coaches): array
    {
        $choices = [];
        foreach ($coaches as $coach) {
            $choices[$coach->getId()] = $coach;
        }
        return $choices;
    }

    #[Route('/consultation/{userId}', name: 'app_planning_show')]
    public function consultation(PlanningRepository $planningRepository, int $userId): Response
    {
        // Récupérer le planning associé à l'utilisateur identifié par $userId
        $planning = $planningRepository->findOneBy(['iduser' => $userId]);
    
        if (!$planning) {
            // Gérer le cas où aucun planning n'est trouvé pour cet utilisateur
            throw $this->createNotFoundException('Aucun planning trouvé pour cet utilisateur.');
        }
    
        return $this->render('planning/consultation.html.twig', [
            'userId' => $userId, // Pass the user ID to the template
            'planning' => $planning,
        ]);
    }
    
      



    #[Route('/{idplanning}/edit', name: 'app_planning_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Planning $planning, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PlanningType::class, $planning);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_planning_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('planning/edit.html.twig', [
            'planning' => $planning,
            'form' => $form,
        ]);
    }

    #[Route('/{idplanning}', name: 'app_planning_delete', methods: ['POST'])]
    public function delete(Request $request, Planning $planning, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $planning->getIdplanning(), $request->request->get('_token'))) {
            $entityManager->remove($planning);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_planning_index', [], Response::HTTP_SEE_OTHER);
    }





    #[Route('/tri/{sortBy?}', name: 'tri')]
    public function tri(PlanningRepository $planningRepository, $sortBy = null)
    {
        // Retrieve sorted plannings using the custom repository method
        $plannings = $planningRepository->sortBy($sortBy);

        // Render the template with sorted plannings
        return $this->render('planning/sorted.html.twig', [
            'plannings' => $plannings,
        ]);
    }
}
