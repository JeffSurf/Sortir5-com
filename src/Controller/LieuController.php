<?php

namespace App\Controller;

use App\Repository\LieuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/lieu', name: 'lieu')]
class LieuController extends AbstractController
{
    #[Route('/ajouter', name: '_create')]
    public function create(): Response
    {
        return $this->render('lieu/lieu.html.twig', [
            'controller_name' => 'LieuController',
        ]);
    }

    #[Route('/getLieuxPourVille/{id}', name: '_getLPV', requirements: ['id' => '\d+'] )]
    public function getLieuxPourVille(LieuRepository $lieuRepository, $id): Response
    {
        $lieux = $lieuRepository->findBy(['ville' => $id]);
        $lieuxArray = [];
        foreach($lieux as $lieu){
            $lieuxArray[$lieu->getId()] = $lieu->getNom();
        }
        return new Response(json_encode(compact('lieuxArray')));
    }
}
