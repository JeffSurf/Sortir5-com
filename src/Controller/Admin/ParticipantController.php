<?php

namespace App\Controller\Admin;

use App\Entity\Participant;
use App\Entity\Site;
use App\Form\ButtonType;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Service\UploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/admin/participant', name: 'app_admin_participant')]
class ParticipantController extends AbstractController {
    #[Route('', name: '_list')]
    public function lister(ParticipantRepository $participantRepository): Response {
        return $this->render('admin/participant/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }

    #[Route('/ajouter', name: '_add')]
    #[Route('/modifier/{id}', name: '_update', requirements: ['id' => '\d+'])]
    public function edit(Request $request, EntityManagerInterface $entityManager,  ParticipantRepository $participantRepository, int $id = null,
                         UploadService $uploadService, UserPasswordHasherInterface $userPasswordHasher): Response {

        $participant = $id == null ? new Participant() : $participantRepository->find($id);
        $msg = $participant->getId() == null ? 'Le participant a été ajouté avec succès !' : 'Le participant a été modifié avec succès !';
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Ajouter ou modifier image de profil
            $inputIMG = $form->get('imageProfil')->getData();

            if ($inputIMG) {
                $currentIMGName = $participant->getImageProfil();
                if ($currentIMGName != 'default_profile_picture.png') { //On supprime l'ancienne du dossier uploads (sauf si c'est celle par défaut)
                    $uploadService->delete($currentIMGName, $this->getParameter('uploads_participants_directory'));
                }

                $newIMGName = $uploadService->uploadFile($inputIMG, $this->getParameter('uploads_participants_directory'));
                $participant->setImageProfil($newIMGName);
            }

            //Mot de passe (si vide on ne le modifie pas)
            if ($form->get('mdp')->getData() != null || $form->get('mdp')->getData() != '' ) {
                $participant->setPassword(
                    $userPasswordHasher->hashPassword(
                        $participant,
                        $form->get('mdp')->getData()
                    )
                );
            }

            $entityManager->persist($participant);
            $entityManager->flush();
            $this->addFlash('success', $msg);
            return $this->redirectToRoute('app_admin_participant_list');
        }

        return $this->render('admin/participant/edit.html.twig', [
            'form' => $form,
            'participant' => $participant,
            'action' => $participant->getId() == null ? 'Ajouter' : 'Modifier'
        ]);
    }

    #[Route('/supprimer/{id}', name: '_delete', requirements: ['id' => '\d+'])]
    public function delete(EntityManagerInterface $entityManager, ParticipantRepository $participantRepository, int $id): Response {

        $participant = $participantRepository->find($id);
        $entityManager->remove($participant);
        $entityManager->flush();
        $this->addFlash('success', 'Le participant a été supprimé avec succès !');
        return $this->redirectToRoute('app_admin_participant_list');
    }

    #[Route('/ajouter/csv', name: '_addbycsv', methods: "POST")]
    public function addByCsv(Request $request, EntityManagerInterface $entityManager, SiteRepository $siteRepository, ValidatorInterface $validator): Response
    {
        $data = $request->getContent();
        $lines = explode(PHP_EOL, $data);

        $error = "";

        foreach ($lines as $key => $line)
        {
            $participant = new Participant();

            $attributes = str_getcsv($line);

            $site = $siteRepository->find($attributes[5]);

            if(!$site)
                return new Response("Ligne $key: Le site avec l'id $attributes[5] n'existe pas", 400);

            $participant
                ->setPrenom($attributes[0])
                ->setNom($attributes[1])
                ->setEmail($attributes[2])
                ->setPseudo($attributes[3])
                ->setRoles(json_decode($attributes[4]))
                ->setSite($site)
                ->setTelephone($attributes[6])
                ->setPassword($attributes[7])
                ->setActif($attributes[8] == 1)
            ;

            $errors = $validator->validate($participant);

            if($errors->count() > 0)
                return new Response( "Ligne $key: " . $errors, 400);

            $entityManager->persist($participant);

        }


        $entityManager->flush();

        return new Response("Votre csv a bien été traité", 200);
    }
}