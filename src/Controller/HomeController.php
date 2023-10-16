<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Service\FirstLoginService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(FirstLoginService $firstLoginService, Request $request): Response {
        /** @var Participant $user */
        $user = $this->getUser();

        if($firstLoginService->checkDefaultPassword($user)) {
            return $this->redirectToRoute("app_profil_edit_password", ['pseudo' => $user->getPseudo()]);
        }

        return $this->render('home/index.html.twig');
    }
}
