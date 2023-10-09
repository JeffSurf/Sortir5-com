<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/lieux', name: 'lieu')]
class LieuController extends AbstractController
{
    #[Route('/create', name: '_create')]
    public function create(): Response
    {
        return $this->render('lieu/lieu.html.twig', [
            'controller_name' => 'LieuController',
        ]);
    }
}
