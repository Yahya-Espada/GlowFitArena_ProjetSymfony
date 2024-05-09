<?php

namespace App\Controller;

use App\Entity\Equipement;
use App\Form\EquipementType;
use App\Repository\EquipementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\QrCodeGenerator;

#[Route('/equipement')]
class EquipementController extends AbstractController
{



    #[Route('/stats', name: 'reservation_stats')]
    public function stats(EquipementRepository $reservationRepository): Response
    {
        $statistics = $reservationRepository->countByType();
        
        return $this->render('reservation/stat.html.twig', [
            'stats' => $statistics,  // Correction : utiliser le même nom que dans le template
        ]);
    }





    #[Route('/', name: 'app_equipement_index', methods: ['GET'])]
    public function index(EquipementRepository $equipementRepository): Response
    {
        return $this->render('equipement/index.html.twig', [
            'equipements' => $equipementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_equipement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,): Response
    {
        $equipement = new Equipement();
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($equipement);
            $entityManager->flush();

            return $this->redirectToRoute('app_equipement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('equipement/new.html.twig', [
            'equipement' => $equipement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_equipement_show', methods: ['GET'])]
    public function show(Equipement $equipement, QrCodeGenerator $qrCodeGenerator):Response
    {        $qrCodeResult = $qrCodeGenerator->createQrCode( $equipement);
        return $this->render('equipement/show.html.twig', [
            'equipement' => $equipement,
            'qrCodeResult' => $qrCodeResult,

        ]);
    }

    #[Route('/{id}/edit', name: 'app_equipement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Equipement $equipement, EntityManagerInterface $entityManager,  QrCodeGenerator $qrCodeGenerator): Response
    {
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_equipement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('equipement/edit.html.twig', [
            'equipement' => $equipement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_equipement_delete', methods: ['POST'])]
    public function delete(Request $request, Equipement $equipement, EntityManagerInterface $entityManager, QrCodeGenerator $qrCodeGenerator): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($equipement);
            $entityManager->flush();
            $qrCodeResult = $qrCodeGenerator->createQrCode($equipement);



            
        }
        return $this->renderForm('reservation/qrcode.html.twig', [
            'qrCodeResult' => $qrCodeResult,
            ]);
        
        return $this->redirectToRoute('app_equipement_index', [], Response::HTTP_SEE_OTHER);
        
    }






    
    #[Route('/search', name:'search_Equipemennt', methods:['GET'])]
     
    public function search(Request $request , EquipementRepository $repository): JsonResponse
    {
        $query = $request->query->get('query');
        // Effectuez ici votre recherche en utilisant $query
      //  $results = "..."; // Résultats de la recherche
      $results = $repository->findOneBy(['id' => $query]);

        return $this->json($results);
    }

    #[Route('/backrechercheAjax', name: 'backrechercheAjax', methods: ['GET'])]
    public function searchAjax(Request $request, EquipementRepository $repo): Response
    {
        $query = $request->query->get('q');
    
        if (empty($query)) {
            $Equipements = $repo->findAll(); // Renvoyer tous les résultats
        } else {
            $Equipements = $repo->findrepasByStats($query); // Recherche par critère
        }
    
        // Rendre le template avec les résultats de la recherche
        $html = $this->renderView("Equipement/index.html.twig", [
            'Equipements' => $Equipements,
        ]);
    
        return new Response($html); // Retourne le HTML comme réponse AJAX
    }
}


