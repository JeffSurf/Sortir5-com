<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\PasswordFormType;
use App\Form\ProfilFormType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Service\FirstLoginService;
use App\Service\UploadService;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Array_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profil', name: 'app_profil_')]
#[IsGranted('ROLE_USER')]
class ProfilController extends AbstractController
{
    #[Route('/{pseudo}', name: 'voir')]
    public function index(ParticipantRepository $participantRepository, SortieRepository $sortieRepository, string $pseudo): Response {
        $requestedUser = $participantRepository->findByPseudo($pseudo);
        /** @var Participant $me */
        $loggedUser = $this->getUser();
        $sorties = $sortieRepository->findAll();

        if ($requestedUser == $loggedUser) {
            echo "C'est mon profil";
        } else {
            $usersMatch = false;
            $users = 0;

            foreach ($sorties as $sortie) {
                $participants = $sortie->getParticipants();
                $organistateur = $sortie->getOrganisateur();
                foreach ($participants as $participant) {
                    if ($requestedUser == $participant && $loggedUser == $organistateur) {
                        echo $requestedUser->getPseudo() . " est inscrit dans une sortie que j'organise.";
                        $usersMatch = true;
                        break;
                    } elseif ($requestedUser == $organistateur && $loggedUser == $participant) {
                        echo $requestedUser->getPseudo() . " organise une sortie à laquelle je participe.";
                        $usersMatch = true;
                        break;
                    } elseif ($requestedUser == $participant || $loggedUser === $participant) {
                        $users++;
                        if ($users == 2) {
                            echo $requestedUser->getPseudo() . " participe avec moi à une sortie";
                            $usersMatch = true;
                            break;
                        }
                    }
                }
            }
            if (!$usersMatch) {
                return $this->render('error/404.html.twig');
            }
        }




        return $this->render('profil/index.html.twig',[
            'user' => $requestedUser
        ]);
    }

    #[Route('/edit/{pseudo}', name: 'editer')]
    public function edit(Request $request, ParticipantRepository $participantRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em, UploadService $fileUploader,  string $pseudo): \Symfony\Component\HttpFoundation\RedirectResponse|Response
    {
        $dataUser = $participantRepository->findByPseudo($pseudo);

        if($dataUser != $this->getUser()) {
            return $this->render('error/404.html.twig');
        }

        $form = $this->createForm(ProfilFormType::class, $dataUser);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            /** @var Participant $user */
            $user = $participantRepository->findByPseudo($pseudo);
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

                $inputIMG = $form->get('image')->getData();

                if($inputIMG) {
                    $currentIMGName = $user->getImageProfil();
                    if ($currentIMGName != 'default_profile_picture.png') {
                        $fileUploader->delete($currentIMGName, $this->getParameter('uploads_participants_directory'));
                    }

                    $newIMGName = $fileUploader->uploadFile($inputIMG, $this->getParameter('uploads_participants_directory'));
                    $user->setImageProfil($newIMGName);
                }
                else
                {
                    $form->get('image')->addError(new FormError("Problème avec l'image"));
                }

                $em->persist($user);
                $em->flush();

                $this->addFlash("success", "Votre profil a bien été modifié !");
                return $this->redirectToRoute("app_profil_voir", ['pseudo' => $user->getPseudo()]);
            }
        }

        return $this->render('profil/edit.html.twig', [
            "profilForm" => $form,
        ]);
    }

    #[Route('/edit/password/{pseudo}', name: 'edit_password')]
    public function editPassword(Request $request, FirstLoginService $firstLoginService, ParticipantRepository $participantRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em, string $pseudo): Response {

        $dataUser = $participantRepository->findByPseudo($pseudo);
        $form = $this->createForm(PasswordFormType::class);

        $form->handleRequest($request);

        if($dataUser != $this->getUser()) {
            return $this->render('error/404.html.twig');
        }

        $msg = null;
        if($firstLoginService->checkDefaultPassword($dataUser)) {
            $msg[0] = "Bienvenue !";
            $msg[1] = "C'est votre première connexion sur notre site, vous devriez modifier votre mot de passe.";
        }


        if($form->isSubmitted() && $form->isValid())
        {
            /** @var Participant $user */
            $user = $participantRepository->findByPseudo($pseudo);

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
                return $this->redirectToRoute("app_profil_voir", ['pseudo' => $user->getPseudo()]);
            }
        }

        return $this->render('profil/password.html.twig', [
            "form" => $form,
            'user' => $dataUser,
            'msg' => $msg
        ]);
    }
}
