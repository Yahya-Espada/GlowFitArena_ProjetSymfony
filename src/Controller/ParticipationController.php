<?php

namespace App\Controller;
use App\Entity\Evenement;
use App\Entity\Participation;
use App\Form\ParticipationType;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Writer\FileWriter;
use Endroid\QrCode\Writer;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\Mime\Attachment;

#[Route('/participation')]
class ParticipationController extends AbstractController
{
    #[Route('/', name: 'app_participation_index', methods: ['GET'])]
    public function index(ParticipationRepository $participationRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $participationRepository->findAll(); // Assuming you have a custom query method in ClubRepository
        $participations = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // Get the current page number from the request, default to 1
            5 // Number of items per page
        );
        return $this->render('participation/index.html.twig', [
            'participations' => $participations,
        ]);
    }

    #[Route('/new', name: 'app_participation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $participation = new Participation();
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($participation);
            $entityManager->flush();
    
            // Récupérer le titre de l'événement associé à la participation
            $titreEvenement = $participation->getEvenement();
    
            // Récupérer l'événement à partir de son titre
            $evenementRepository = $this->getDoctrine()->getRepository(Evenement::class);
            $evenement = $evenementRepository->findOneBy(['categorie' => $titreEvenement]);
    
            if ($evenement !== null) {
                // Générer le contenu du code QR
                $qrContent = "Titre: " . $evenement->getTitre() . "\n"
                    . "Description: " . $evenement->getDescription() . "\n"
                    . "Gagnant: " . $evenement->getWinner() . "\n"
                    . "Date: " . $evenement->getDate()->format('Y-m-d H:i:s') . "\n"
                    . "Catégorie: " . $evenement->getCategorie() . "\n";
    
                // Générer le code QR
                $qrCode = new QrCode($qrContent);
                $qrCodeWriter = new PngWriter();
                $qrCodeImage = $qrCodeWriter->write($qrCode)->getString();
    
                // Créer un message e-mail
                $email = (new Email())
                    ->from('bchirben8@gmail.com')
                    ->to($participation->getEmail())
                    ->subject('Confirmation de participation')
                    ->text('Merci pour votre inscription à notre événement.')
                    ->embed($qrCodeImage, 'qr-code.png', 'image/png');
    
                $mailer->send($email);
            } else {
                // Gérer le cas où aucun événement correspondant n'est trouvé
                // Vous pouvez logger un message d'erreur ou prendre d'autres mesures nécessaires
            }
    
            return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('participation/new.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }
    

    #[Route('/newpartfront', name: 'app_participation_newpartfront', methods: ['GET', 'POST'])]
    public function newpartfront(Request $request, EntityManagerInterface $entityManager): Response
    {
        $participation = new Participation();
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($participation);
            $entityManager->flush();

            return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participation/new.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_participation_show', methods: ['GET'])]
    public function show(Participation $participation): Response
    {
        return $this->render('participation/show.html.twig', [
            'participation' => $participation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_participation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participation/edit.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_participation_delete', methods: ['POST'])]
    public function delete(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($participation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
    }
}
