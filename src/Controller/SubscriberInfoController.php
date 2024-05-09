<?php

namespace App\Controller;

use App\Entity\SubscriberInfo;
use App\Entity\User;
use App\Form\SubscriberInfoType;
use Doctrine\ORM\EntityManagerInterface;
use mofodojodino\ProfanityFilter\Check;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/subscriber/info')]
class SubscriberInfoController extends AbstractController
{
    #[Route('/', name: 'app_subscriber_info_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $subscriberInfos = $entityManager
            ->getRepository(SubscriberInfo::class)
            ->findAll();

        return $this->render('subscriber_info/index.html.twig', [
            'subscriber_infos' => $subscriberInfos,
        ]);
    }

    #[Route('/new', name: 'app_subscriber_info_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $subscriberInfo = new SubscriberInfo();

        $users = $entityManager->getRepository(User::class)->findBy(['role' => 'user']);


        $form = $this->createForm(SubscriberInfoType::class, $subscriberInfo , [
            'user_choices' => $users,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $fieldsToFilter = ['goals', 'dietaryRestrictions', 'foodPreferences'];
            foreach ($fieldsToFilter as $field) {
                $value = $subscriberInfo->{'get'.ucfirst($field)}();
                if ((new \mofodojodino\ProfanityFilter\Check)->hasProfanity($value)) {
                    $form->addError(new FormError('Profanity detected in ' . $field . '. Please remove inappropriate language.'));
                    return $this->renderForm('subscriber_info/new.html.twig', [
                        'subscriber_info' => $subscriberInfo,
                        'form' => $form,
                    ]);
                }
                $subscriberInfo->{'set'.ucfirst($field)}($value);
            }

            $userId = $form->get('user')->getData();

            // Check if a SubscriberInfo with the same user_id already exists
            $existingSubscriberInfo = $entityManager->getRepository(SubscriberInfo::class)->findOneBy(['user' => $userId]);
            if (!$existingSubscriberInfo) {
                $entityManager->persist($subscriberInfo);
                $entityManager->flush();

                return $this->redirectToRoute('app_subscriber_info_index', [], Response::HTTP_SEE_OTHER);
            }

            // Add an error message to the form
            $form->addError(new FormError('A SubscriberInfo with this user_id already exists.'));
        }

        return $this->renderForm('subscriber_info/new.html.twig', [
            'subscriber_info' => $subscriberInfo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_subscriber_info_show', methods: ['GET'])]
    public function show(SubscriberInfo $subscriberInfo): Response
    {
        return $this->render('subscriber_info/show.html.twig', [
            'subscriber_info' => $subscriberInfo,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_subscriber_info_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SubscriberInfo $subscriberInfo, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SubscriberInfoType::class, $subscriberInfo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_subscriber_info_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('subscriber_info/edit.html.twig', [
            'subscriber_info' => $subscriberInfo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_subscriber_info_delete', methods: ['POST'])]
    public function delete(Request $request, SubscriberInfo $subscriberInfo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subscriberInfo->getId(), $request->request->get('_token'))) {
            $entityManager->remove($subscriberInfo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_subscriber_info_index', [], Response::HTTP_SEE_OTHER);
    }
}
