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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/participant', name: 'participant')]
class ParticipantController extends AbstractController {
    #[Route('/', name: '_list')]
    public function lister(ParticipantRepository $participantRepository): Response {
        return $this->render('participant/list.html.twig', [
            'champions' => $participantRepository->findAll(),
        ]);
    }

    #[Route('/ajouter', name: '_add')]
    #[Route('/modifier/{id}', name: '_update')]
    public function edit(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, UploadService $uploadService, Participant $participant): Response {

        $msg = $participant->getId() == null ? 'Le participant a été ajouté avec succès !' : 'Le participant a été modifié avec succès !';
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('imageProfil')->getData();

            if ($file) {
                $newFilename = $uploadService->uploadFile($file, $this->getParameter('uploads_participants_directory'));
                $participant->setImageProfil($newFilename);
            }


            $entityManager->persist($participant);
            $entityManager->flush();
            $this->addFlash('success', $msg);
            return $this->redirectToRoute('participant_list');
        }

        return $this->render('participant/add.html.twig', [
            'form' => $form,
            'champion' => $participant->getNom(),
            'action' => $participant->getId() == null ? 'Ajouter' : 'Modifier'
        ]);
    }

    #[Route('/supprimer/{id}', name: '_delete')]
    public function delete(EntityManagerInterface $entityManager, Participant $participant): Response {

        $entityManager->remove($participant);
        $entityManager->flush();
        $this->addFlash('success', 'Le participant a été supprimé avec succès !');
        return $this->redirectToRoute('participant_list');
    }
}