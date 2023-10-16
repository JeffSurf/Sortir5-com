<?php

namespace App\Controller\Admin;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Form\SearchFormType;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Service\ParticipantService;
use App\Service\UploadService;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/admin/participant', name: 'app_admin_participant')]
class ParticipantController extends AbstractController {
    #[Route('', name: '_list')]
    public function lister(Request $request, ParticipantRepository $participantRepository): Response {
        $participants = $participantRepository->findAll();

        //filter
        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);
        $searchValue = $searchForm->get('search')->getData();

        if ($searchForm->isSubmitted() && $searchForm->isValid() && $searchValue != '') {
            $participants = $participantRepository->filterByText($searchValue);
        }

        return $this->render('admin/participant/index.html.twig', [
            'participants' => $participants,
            'searchForm' => $searchForm
        ]);
    }

    #[Route('/ajouter', name: '_add')]
    #[Route('/modifier/{id}', name: '_update', requirements: ['id' => '\d+'])]
    public function edit(Request $request, EntityManagerInterface $entityManager,  ParticipantRepository $participantRepository,
                         UploadService $uploadService, UserPasswordHasherInterface $userPasswordHasher, int $id = null): Response {

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

            $isActif = $form->get('actif')->getData();

            if(!$isActif && !in_array("ROLE_ADMIN", $participant->getRoles()))
            {
                $participant->setRoles(["ROLE_BAN"]);
            }
            else
            {
                $participant->setRoles(["ROLE_USER"]);
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
    public function delete(EntityManagerInterface $entityManager, ParticipantRepository $participantRepository, ParticipantService $participantService, int $id): Response {

        $participant = $participantRepository->find($id);

        foreach ($participant->getSorties() as $sortie) {
            $participantService->archive($participant, $sortie);
        }

        foreach ($participant->getSortiesOrganisateur() as $sortie) {
            foreach ($sortie->getParticipants() as $p) {
                $participantService->archive($p, $sortie);
            }
        }

        $entityManager->remove($participant);
        $entityManager->flush();
        $this->addFlash('success', 'Le participant a été supprimé avec succès !');
        return $this->redirectToRoute('app_admin_participant_list');
    }

    #[Route('/ajouter/csv', name: '_addbycsv', methods: "POST")]
    public function addByCsv(Request $request, EntityManagerInterface $entityManager, SiteRepository $siteRepository, ValidatorInterface $validator): Response
    {
        $data = $request->getContent();

        $reader = Reader::createFromString($data);
        $reader->setHeaderOffset(0);
        $lines = $reader->getRecords();

        $error = "";

        foreach ($lines as $key => $attributes)
        {
            $participant = new Participant();

            $site = $siteRepository->find($attributes["site"]);

            if(!$site)
                return new Response("Ligne $key: Le site n'existe pas " . "id: " . $attributes["site"], 400);

            // prenom,nom,email,pseudo,roles,site,telephone,password,actif,imageProfil,rgpd
            $participant
                ->setPrenom($attributes["prenom"])
                ->setNom($attributes["nom"])
                ->setEmail($attributes["email"])
                ->setPseudo($attributes["pseudo"])
                ->setRoles(json_decode($attributes["roles"]))
                ->setSite($site)
                ->setTelephone($attributes["telephone"])
                ->setPassword($attributes["password"])
                ->setImageProfil($attributes["imageProfil"])
                ->setRgpd($attributes["rgpd"])
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