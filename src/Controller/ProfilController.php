<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\PasswordFormType;
use App\Form\ProfilFormType;
use App\Service\UploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
    public function index(): Response
    {
        return $this->render('profil/index.html.twig');
    }

    #[Route('/edit', name: 'editer')]
    public function edit(Request $request,  UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em, UploadService $fileUploader): \Symfony\Component\HttpFoundation\RedirectResponse|Response
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
                $form->get("plainPassword")->addError(new FormError( "Le mot de passe n'est pas correct"));
            }
            else
            {
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

                /**
                 * @var UploadedFile $imageFile
                 */
                $imageFile = $form->get('image')->getData();

                if($imageFile)
                {
                    $imageFilename = $fileUploader->uploadFile($imageFile, $this->getParameter('uploads_participants_directory'));

                    $fileUploader->delete($user->getImageProfil(), $this->getParameter('uploads_participants_directory'));

                    $user->setImageProfil($imageFilename);
                }
                else
                {
                    $form->get('image')->addError(new FormError("Problème avec l'image"));
                }

                $em->persist($user);
                $em->flush();

                $this->addFlash("success", "Votre profil a bien été modifié !");
                return $this->redirectToRoute("app_profil_voir");
            }
        }

        return $this->render('profil/edit.html.twig', [
            "profilForm" => $form
        ]);
    }

    #[Route('/edit/password', name: 'edit_password')]
    public function editPassword(Request $request,  UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em): Response {
        $form = $this->createForm(PasswordFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            /** @var Participant $user */
            $user = $this->getUser();

            if(!$userPasswordHasher->isPasswordValid($user, $form->get('oldPassword')->getData()))
            {
                $form->get("oldPassword")->addError(new FormError( "L'ancien mot de passe n'est pas correct"));
            }
            else
            {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('newPassword')->getData())
                );

                $em->persist($user);
                $em->flush();

                $this->addFlash("success", "Votre mot de passe a bien été modifié !");
                return $this->redirectToRoute("app_profil_voir");
            }
        }

        return $this->render('profil/password.html.twig', [
            "form" => $form
        ]);
    }
}
