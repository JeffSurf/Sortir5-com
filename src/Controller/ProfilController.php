<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ProfilFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
        $dataUser = $this->getUser();

        $form = $this->createForm(ProfilFormType::class, $dataUser);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            /** @var Participant $user */
            $user = $this->getUser();
            if(!$userPasswordHasher->isPasswordValid($user, $form->get('plainPassword')->getData()))
            {
                $this->addFlash("danger", "Le mot de passe n'est pas valide");
                throw new HttpException(400, "Le mot de passe n'est pas valide");
            }

            if($this->isGranted("ROLE_ADMIN"))
            {
                $user = $dataUser;
            }
            else
            {
                $user
                    ->setTelephone($dataUser->getTelephone())
                    ->setPseudo($dataUser->getPseudo())
                    ->setEmail($dataUser->getEmail());
            }

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute("app_profil_voir");
        }

        return $this->render('profil/edit.html.twig', [
            "profilForm" => $form
        ]);
    }
}
