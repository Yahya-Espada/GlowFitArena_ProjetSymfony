<?php

namespace App\Controller;

use App\Entity\DietaryProgramsCopy;
use App\Entity\User;
use App\Form\DietaryProgramsCopyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


#[Route('/dietaryprogramscopy')]
class DietaryProgramsCopyController extends AbstractController
{
    #[Route('/', name: 'app_dietary_programs_copy_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $dietaryProgramsCopies = $entityManager
            ->getRepository(DietaryProgramsCopy::class)
            ->findAll();

        return $this->render('dietary_programs_copy/index.html.twig', [
            'dietary_programs_copies' => $dietaryProgramsCopies,
        ]);
    }

    #[Route('/new', name: 'app_dietary_programs_copy_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $dietaryProgramsCopy = new DietaryProgramsCopy();
        // Get only users with the role "user"
        $users = $entityManager->getRepository(User::class)->findBy(['role' => 'user']);
        $coaches = $entityManager->getRepository(User::class)->findBy(['role' => 'coach']);


        $form = $this->createForm(DietaryProgramsCopyType::class, $dietaryProgramsCopy, [
            'coach_choices' => $coaches,
            'user_choices' => $users,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($dietaryProgramsCopy);
            $entityManager->flush();

            return $this->redirectToRoute('app_dietary_programs_copy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dietary_programs_copy/new.html.twig', [
            'dietary_programs_copy' => $dietaryProgramsCopy,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dietary_programs_copy_show', methods: ['GET'])]
    public function show(DietaryProgramsCopy $dietaryProgramsCopy): Response
    {
        return $this->render('dietary_programs_copy/show.html.twig', [
            'dietary_programs_copy' => $dietaryProgramsCopy,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dietary_programs_copy_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DietaryProgramsCopy $dietaryProgramsCopy, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DietaryProgramsCopyType::class, $dietaryProgramsCopy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_dietary_programs_copy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dietary_programs_copy/edit.html.twig', [
            'dietary_programs_copy' => $dietaryProgramsCopy,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dietary_programs_copy_delete', methods: ['POST'])]
    public function delete(Request $request, DietaryProgramsCopy $dietaryProgramsCopy, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dietaryProgramsCopy->getId(), $request->request->get('_token'))) {
            $entityManager->remove($dietaryProgramsCopy);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_dietary_programs_copy_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/{id}/export', name: 'app_dietary_programs_copy_export', methods: ['GET'])]
    public function export(DietaryProgramsCopy $dietaryProgramsCopy , MailerInterface $mailer): Response
    {
        // Generate HTML from the new view
        $html = $this->renderView('dietary_programs_copy/pdf.html.twig', [
            'dietary_programs_copy' => $dietaryProgramsCopy,
        ]);

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Set up the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Store PDF Binary Data
        $output = $dompdf->output();

        // In this part, we are writing the file in the path you specified
        // plus the name and the extension that you wish your file to have
        $publicDirectory = $this->getParameter('kernel.project_dir').'/public';
        $pdfFilepath  =  $publicDirectory .  '/pdf/dietary_program_' . $dietaryProgramsCopy->getId() . '.pdf';

        // Write file to the desired path
        file_put_contents($pdfFilepath, $output);

        // Create a new email
        $email = (new Email())
            ->from('attiayasmine38@gmail.com')
           // ->to($dietaryProgramsCopy->getUser()->getEmail())
            ->to('yahyawafi12@gmail.com')
            ->subject('Your Dietary Program')
            ->text('Find attached your dietary program.')
            ->html(' <div style="font-family: Arial, sans-serif; color: #333; padding: 20px; border: 1px solid #ddd; border-radius: 5px; max-width: 600px; margin: auto;">
            <h2 style="font-size: 24px; color: #444; text-align: center;">Dietary Program</h2>
            <p style="font-size: 16px; color: #666; line-height: 1.5;">
                Find attached your dietary program.
            </p>
            <p style="font-size: 14px; color: #999; line-height: 1.5;">
                If you have any questions, feel free to reply to this email.
            </p>
            <div style="margin-top: 20px; text-align: center;">
                <a href="#" style="background-color: #008CBA; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border: none; border-radius: 4px;">View Program</a>
            </div>
        </div>')
            ->attachFromPath($pdfFilepath);

        // Send the email
        try {
            $mailer->send($email);
        }catch (\Exception $e){
            error_log($e->getMessage());
        }
        // Delete the temporary file

        // Send some text response
        $this->addFlash('success', 'The PDF file has been successfully generated!');
        return $this->redirectToRoute('app_landing_page');    }
}
