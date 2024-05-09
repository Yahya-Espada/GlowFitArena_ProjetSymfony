<?php

namespace App\Controller;


use App\Entity\Evenement;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_evenement_search')]
    public function searchUser(Request $request, EvenementRepository $repository,PaginatorInterface $paginator): Response
    {
        $query = $request->request->get('query');
        $evenements = $paginator ->paginate(
            $repository->searchByNom($query),
            $request->query->getInt('page', 1),);
        return $this->render('evenement/_search_results.html.twig', [
            'evenements' => $evenements
        ]);
    }
}