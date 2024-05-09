<?php

namespace App\Controller;

use App\Entity\Seance;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Planning;
use App\Form\SeanceType;
use App\Repository\SeanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use Dompdf\Dompdf;


#[Route('/seance')]
class SeanceController extends AbstractController
{
    #[Route('/', name: 'app_seance_index', methods: ['GET'])]
    public function index(SeanceRepository $seanceRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Récupérer toutes les séances à partir du dépôt
        $seancesQuery = $seanceRepository->findAll();

        // Paginer les séances
        $pagination = $paginator->paginate(
            $seancesQuery, // Query des séances
            $request->query->getInt('page', 1), // Numéro de page
            5 // Nombre d'éléments par page
        );

        // Rendre la vue avec les séances paginées
        return $this->render('seance/index.html.twig', [
            'pagination' => $pagination,
            'seances' => $pagination->getItems() // Passer les séances paginées à la vue
        ]);
    }

    #[Route('/new', name: 'app_seance_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $seance = new Seance();
        $form = $this->createForm(SeanceType::class, $seance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $planningId = $form->get('idPlanning')->getData();

            // Retrieve the Planning entity
            $planning = $entityManager->getRepository(Planning::class)->find($planningId);

            // Ensure the Planning entity is correctly associated with the Seance
            $seance->setIdPlanning($planning);
        }

        return $this->renderForm('seance/new.html.twig', [
            'seance' => $seance,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_seance_show', methods: ['GET'])]
    public function show(Seance $seance): Response
    {
        return $this->render('seance/show.html.twig', [
            'seance' => $seance,
            // Utilisez la méthode correcte pour accéder à l'objet Planning associé
            'planning' => $seance->getIdPlanning(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_seance_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Seance $seance, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SeanceType::class, $seance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Assurez-vous que le formulaire récupère correctement l'objet Planning
            // à partir de l'identifiant fourni dans le formulaire
            $planningId = $form->get('idPlanning')->getData();
            $planning = $entityManager->getRepository(Planning::class)->find($planningId);

            // Assurez-vous que l'objet Planning est correctement associé à la séance
            $seance->setIdPlanning($planning);

            $entityManager->flush();

            return $this->redirectToRoute('app_seance_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('seance/edit.html.twig', [
            'seance' => $seance,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_seance_delete', methods: ['POST'])]
    public function delete(Request $request, Seance $seance, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $seance->getId(), $request->request->get('_token'))) {
            $entityManager->remove($seance);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_seance_index', [], Response::HTTP_SEE_OTHER);
    }





    private function isRecaptchaValid($recaptchaResponse)
    {
        $secretKey = '6Le4kswpAAAAANOa0JB3MpqKwNMe1ii8IDr1Fd23';
        $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';
        $postData = http_build_query([
            'secret' => $secretKey,
            'response' => $recaptchaResponse
        ]);

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $postData
            ]
        ]);

        $response = file_get_contents($recaptchaUrl, false, $context);
        $result = json_decode($response);

        return $result->success;
    }




    #[Route(path: '/seance/ww', name: 'w')]
    public function w(): Response
    {
        // Get the list of users from the database
        $SeanceRepository = $this->getDoctrine()->getRepository(Seance::class);
        $seances = $SeanceRepository->findAll();

        // Render the PDF template with user data
        $html = $this->renderView('seance/list.html.twig', [
            'seances' => $seances,
        ]);

        // Instantiate Dompdf
        $dompdf = new Dompdf();

        // Load HTML content
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF
        $dompdf->render();

        // Generate filename
        $filename = 'Seance_List' . date('Ymd_His') . '.pdf';

        // Output PDF to browser for download
        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]
        );
    }
}
