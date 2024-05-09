<?php

namespace App\Controller;

use App\Entity\Seance;
use App\Entity\Planning;
use App\Form\SeanceType;
use App\Repository\SeanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;



class pdfController extends AbstractController
{


    #[Route(path: '/pdf1', name: 'pdf1')]
    public function pdf(): Response
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
