<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use App\Service\UploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/participant', name: 'participant')]
class ParticipantController extends AbstractController {
    #[Route('/', name: '_list')]
    public function lister(ParticipantRepository $participantRepository): Response {
        return $this->render('participant/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }

    #[Route('/ajouter', name: '_add')]
    #[Route('/modifier/{id}', name: '_update', requirements: ['id' => '\d+'])]
    public function edit(Request $request, EntityManagerInterface $entityManager,  ParticipantRepository $participantRepository, int $id = null ,
                         UploadService $uploadService, UserPasswordHasherInterface $userPasswordHasher): Response {

        $participant = $id == null ? new Participant() : $participantRepository->find($id);
        $msg = $participant->getId() == null ? 'Le participant a été ajouté avec succès !' : 'Le participant a été modifié avec succès !';
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('imageProfil')->getData();

            if ($file) {
                $newFilename = $uploadService->uploadFile($file, $this->getParameter('uploads_participants_directory'));
                $participant->setImageProfil($newFilename);
            }

            if ($form->get('mdp')->getData() != null) {
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
            return $this->redirectToRoute('participant_list');
        }

        return $this->render('participant/edit.html.twig', [
            'form' => $form,
            'action' => $participant->getId() == null ? 'Ajouter' : 'Modifier'
        ]);
    }

    #[Route('/supprimer/{id}', name: '_delete', requirements: ['id' => '\d+'])]
    public function delete(EntityManagerInterface $entityManager, ParticipantRepository $participantRepository, int $id): Response {

        $participant = $participantRepository->find($id);
        $entityManager->remove($participant);
        $entityManager->flush();
        $this->addFlash('success', 'Le participant a été supprimé avec succès !');
        return $this->redirectToRoute('participant_list');
    }
}