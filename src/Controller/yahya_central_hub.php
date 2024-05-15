<?php

namespace App\Controller;

use App\Entity\DietaryProgramsCopy;
use App\Entity\SubscriberInfo;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class yahya_central_hub extends AbstractController
{
    /**
     * @Route("/central-hub", name="app_central_hub")
     */
    public function centralHub(): Response
    {
        return $this->render('central_hub.html.twig');
    }

    /**
     * @Route("/landing-page", name="app_landing_page")
     */

    public function landingPage(SessionInterface $sessionInterface, UserRepository $userRepository): Response
    {
        /**
         * @Route("/subinfoshow", name="app_landing_page_subinfoshow")
         */
        $user = UserController::getConnectedUser($sessionInterface, $userRepository);
        if (!$user) {
            return $this->redirectToRoute("app_user_login");
        }

        $entityManager = $this->getDoctrine()->getManager();
        $subscriberInfo = $entityManager
            ->getRepository(SubscriberInfo::class)
            ->findOneBy(['user' => $user]);

        $dietaryProgramcopy = $entityManager
            ->getRepository(DietaryProgramsCopy::class)
            ->findOneBy(['subscriber' => $user]);
        if (!$dietaryProgramcopy) {
            $this->addFlash('warning', 'You need to create a Dietary Program.');
            return $this->redirectToRoute('app_user_profil');

            throw new NotFoundHttpException('You need to create a Dietary Program.');

        }
        if (!$subscriberInfo) {
            // display a message for the user for him to know that he should add a new subscriber info
            $this->addFlash('warning', 'You need to create a Subscriber Info.');
            return $this->redirectToRoute('app_user_profil');

            throw new NotFoundHttpException('You need to create a Subscriber Info.');
        }

        return $this->render('Yahya_User_landing_page.html.twig', [
            'WithconnectedUser' => true,
            'subscriber_info' => $subscriberInfo,
            'dietary_program' => $dietaryProgramcopy
        ]);
    }
}