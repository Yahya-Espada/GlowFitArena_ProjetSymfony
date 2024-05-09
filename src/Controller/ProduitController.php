<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Dompdf\Dompdf;
use App\Service\TwilioService;


// Include the Composer autoloader
require_once __DIR__.'/../../vendor/autoload.php';





#[Route('/produit')]
class ProduitController extends AbstractController
{
    

    #[Route('/', name: 'app_produit_index', methods: ['GET'])]
    public function index(Request $request, ProduitRepository $produitRepository): Response
    {
        $searchQuery = $request->query->get('search', '');
        $sortBy = $request->query->get('sort', 'id'); // Default sorting by id if not specified
        $sortOrder = $request->query->get('order', 'ASC'); // Default sorting order is ascending if not specified
    
        // Find products based on the search query and sorting parameters
        $produits = $produitRepository->findBySearchAndSort($searchQuery, $sortBy, $sortOrder);
    
        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
            'searchQuery' => $searchQuery,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
    }

    #[Route('/sorted', name: 'app_produit_index_sorted', methods: ['GET'])]
    public function indexSorted(Request $request, ProduitRepository $produitRepository): Response
    {
        $sortBy = $request->query->get('sort', 'id'); // Default sorting by id if not specified
        $sortOrder = $request->query->get('order', 'ASC'); // Default sorting order is ascending if not specified
    
        // Find products sorted by the specified parameters
        $produits = $produitRepository->findAllSorted($sortBy, $sortOrder);
    
        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
    }
    
    #[Route('/produit/afficherfront', name: 'app_produit_index1', methods: ['GET'])]
    public function front(Request $request, ProduitRepository $produitRepository): Response
    {
        $searchQuery = $request->query->get('search');
        
        if ($searchQuery) {
            $produits = $produitRepository->searchByKeyword($searchQuery);
        } else {
            $produits = $produitRepository->findAll();
        }
        
        return $this->render('index1.html.twig', [
            'produits' => $produits,
            'searchQuery' => $searchQuery,
        ]);
    }

    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageFile')->getData();
    
            // Check if a file was uploaded
            if ($imageFile) {
                // Generate a unique name for the file
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
    
                // Move the file to the directory where images are stored
                $imageFile->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );
    
                // Update the 'photoProduit' property with the filename
                $produit->setPhotoProduit($newFilename);
            }
    
            // Persist the product entity
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();

           
    
            // Redirect to the index page after successful submission
            return $this->redirectToRoute('app_produit_index');
        }
    
        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/{idProduit}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager,$idProduit): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index');
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{idProduit}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getIdProduit(), $request->request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_index');
    }

    #[Route('/produit/downloadpdf', name: 'pdf', methods: ['GET'])]
public function d(ProduitRepository $produitRepository): Response
{
    // Get the list of produits from the database
    
    $produits = $produitRepository->findAllSorted();

    // Render the PDF template with produit data
    $html = $this->renderView('produit/pdf.html.twig', [
        'produits' => $produits,
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
    $filename = 'Produit_List_' . date('Ymd_His') . '.pdf';

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

#[Route('/stats', name: 'app_produit_stats', methods: ['GET'])]
    public function stats(ProduitRepository $produitRepository): Response
    {
        // Retrieve statistics about your products
        $stats = $produitRepository->getProductStats();

        return $this->render('produit/stats.html.twig', [
            'stats' => $stats,
        ]);
    }

    #[Route('/map', name: 'map_page')]
    public function index3(): Response
    {
        return $this->render('map/index.html.twig');
    }

    }















