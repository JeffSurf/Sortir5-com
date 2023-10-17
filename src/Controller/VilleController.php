<?php

namespace App\Controller;

use App\Repository\LieuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/ville", name: "app_ville")]
class VilleController extends AbstractController
{
    #[Route('/{id}/lieu', name: '_lieu')]
    public function getLieuxPourVille(LieuRepository $lieuRepository, $id): Response
    {
        $lieux = $lieuRepository->findBy(['ville' => $id]);
        $lieuxArray = [];

        foreach($lieux as $lieu){
            $lieuxArray[] = ['id'=> $lieu->getId(), 'nom' => $lieu->getNom()];
        }
        return $this->json($lieuxArray);
    }
}