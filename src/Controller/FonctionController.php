<?php

namespace App\Controller;

use App\Entity\Equipement;


use App\Repository\EquipementRepository;

use Symfony\Component\HttpFoundation\Request; // Assurez-vous d'importer la classe Request depuis le composant HttpFoundation
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FonctionController extends AbstractController
{
    #[Route('/fonction', name: 'app_fonction')]
    public function index(): Response
    {
        return $this->render('fonction/index.html.twig', [
            'controller_name' => 'FonctionController',
        ]);
    }


     /**
     * @Route("/TrierspcASC", name="triespc",methods={"GET"})
     */
    public function trierSpecialite(Request $request, EquipementRepository $equipementRepository): Response
    {
        // Utilisez directement la méthode trie() du repository
        $arb = $equipementRepository->trie();

        return $this->render('equipement/index.html.twig', [
            'equipements' => $arb,
        ]);
    }

 /**
     * @Route("/TrierspcDESC", name="triespcDESC",methods={"GET"})
     */
    public function trierSp(Request $request, EquipementRepository $equipementRepository): Response
    {
        // Utilisez directement la méthode trie() du repository
        $arb = $equipementRepository->trieDes();

        return $this->render('equipement/index.html.twig', [
            'equipements' => $arb,
        ]);
    }

 /**
     * @Route("/Trieprix", name="trieprix",methods={"GET"})
     */
    public function trierprode(Request $request, EquipementRepository $equipementRepository): Response
    {
        // Utilisez directement la méthode trie() du repository
        $arb = $equipementRepository->trierequipement();

        return $this->render('equipement/index.html.twig', [
            'equipement' => $arb,
        ]);
    }


    // /**
    //  * @Route("/Trieprixdes", name="trieprixdes",methods={"GET"})
    //  */
    // public function trierproddes(Request $request, ProductRepository $categorieRepository): Response
    // {
    //     // Utilisez directement la méthode trie() du repository
    //     $arb = $categorieRepository->trieproddes();

    //     return $this->render('product/index.html.twig', [
    //         'products' => $arb,
    //     ]);
    // }




    


    
// /**
//      * @Route("/stats", name="stats")
//      */
//     public function statistiques(RepasRepository $footRepo){
//         // On va chercher toutes les menus
//         $menus = $footRepo->findAll();

// //Data Category
//         $foot = $footRepo->createQueryBuilder('a')
//             ->select('count(a.id)')
//             ->Where('a.type= :type')
//             ->setParameter('type',"client")
//             ->getQuery()
//             ->getSingleScalarResult();

//         $hand = $footRepo->createQueryBuilder('a')
//             ->select('count(a.id)')
//             ->Where('a.type= :type')
//             ->setParameter('type',"admin")
//             ->getQuery()
//             ->getSingleScalarResult();
//         $volley= $footRepo->createQueryBuilder('a')
//             ->select('count(a.id)')
//             ->Where('a.type= :type')
//             ->setParameter('type',"hhhhh")
//             ->getQuery()
//             ->getSingleScalarResult();

       

//         return $this->render('Stats/stats.html.twig', [
//             'nfoot' => $foot,
//             'nhand' => $hand,
//             'nvol' => $volley,


//         ]);
//     }


}