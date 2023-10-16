<?php

namespace App\Controller;

use App\Repository\LieuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
}
