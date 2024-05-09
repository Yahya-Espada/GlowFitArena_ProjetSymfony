<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Dompdf\Dompdf;



class EvenementController extends AbstractController
{

    #[Route('/evenement/search/ajax', name: 'evenement_search_ajax', methods: ['GET'])]
    public function searchAjax(Request $request, EvenementRepository $Repository): Response
    {
        $query = $request->query->get('query');
        $results = $Repository->searchByName($query);

        return $this->render('evenement/_search_results.html.twig', [
            'results' => $results,
        ]);
    }
    #[Route('evenement/', name: 'app_evenement_index', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository, PaginatorInterface $paginator, Request $request): Response
    {

        $evenements = $evenementRepository->findAllSortedByTitre();

        //$query = $evenementRepository->findAll(); // Assuming you have a custom query method in ClubRepository
        $evenements = $paginator->paginate(
            $evenements,
            $request->query->getInt('page', 1), // Get the current page number from the request, default to 1
            8 // Number of items per page
        );
        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    #[Route('/pdf', name: 'app_pdf_generate', methods: ['GET'])]
    public function generatePdf(EvenementRepository $Repository): Response
{
    // Récupérez la liste des clubs depuis le repository
    $evenements = $Repository->findAll();

    // Créez une instance de Dompdf
    $dompdf = new Dompdf();

    // Générez le contenu HTML pour le PDF
    $htmlContent = $this->renderView('evenement/pdf.html.twig', [
        'evenements' => $evenements,
    ]);

    // Chargez le contenu HTML dans Dompdf
    $dompdf->loadHtml($htmlContent);

    // Réglez les options de Dompdf si nécessaire
    $dompdf->setPaper('A4', 'portrait');

    // Rendu du PDF
    $dompdf->render();

    // Obtenez le contenu PDF généré
    $pdfContent = $dompdf->output();

    // Créez une réponse Symfony pour retourner le PDF au navigateur
    $response = new Response($pdfContent);
    $response->headers->set('Content-Type', 'application/pdf');

    // Facultatif : téléchargement du PDF au lieu de l'afficher dans le navigateur
    // $response->headers->set('Content-Disposition', 'attachment; filename="liste_clubs.pdf"');

    return $response;
}


    #[Route('/evenement/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload

            $uploadedFile = $form->get('image')->getData();
            if ($uploadedFile) {
                $newFilename = uniqid().'.'.$uploadedFile->guessExtension();
                $uploadedFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
                $evenement->setImage($newFilename);
            }

            // Convert date to string format

            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }


    #[Route('/evenement/{id}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/evenement/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/evenement/{id}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('event/', name: 'app_evenement_indexFront', methods: ['GET'])]
    public function indexFront(EvenementRepository $evenementRepository, PaginatorInterface $paginator, Request $request): Response
    {

        $evenements = $evenementRepository->findAllSortedByTitre();

        //$query = $evenementRepository->findAll(); // Assuming you have a custom query method in ClubRepository
        $evenements = $paginator->paginate(
            $evenements,
            $request->query->getInt('page', 1), // Get the current page number from the request, default to 1
            8 // Number of items per page
        );
        return $this->render('evenement/indexFront.html.twig', [
            'evenements' => $evenements,
        ]);
    }

}
