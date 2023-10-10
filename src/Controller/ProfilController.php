<?php

namespace App\Controller;

use App\Form\ProfilFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profil', name: 'app_profil_')]
#[IsGranted('ROLE_USER')]
class ProfilController extends AbstractController
{
    #[Route('', name: 'voir')]
    public function voir(): Response
    {
        return $this->render('profil/index.html.twig');
    }

    #[Route('/edit', name: 'editer')]
    public function editer(Request $request,  UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em): \Symfony\Component\HttpFoundation\RedirectResponse|Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ProfilFormType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute("app_profil_voir");
        }

        return $this->render('profil/edit.html.twig', [
            "profilForm" => $form
        ]);
    }
}
