<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\AnnulerSortieType;
use App\Form\FilterFormType;
use App\Form\SortieFormType;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use App\Service\FirstLoginService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/sortie', name: 'sortie')]
class SortieController extends AbstractController
{
    #[Route('', name: '_list')]
    public function list(
        Request $request,
        FirstLoginService $firstLoginService,
        SortieRepository $sortieRepository,
        UserInterface $user,
        ParticipantRepository $participantRepository
            ): Response
    {

        //Verifier première connexion
        /** @var Participant $user */
        $user = $this->getUser();
        if ($firstLoginService->checkDefaultPassword($user)) {
            return $this->redirectToRoute("app_profil_edit_password", ['pseudo' => $user->getPseudo()]);
        }

        $now = new \DateTime();
        $site = $participantRepository->find($user)->getSite();

        //filter
        $filterform = $this->createForm(FilterFormType::class);
        $filterform->get('site')->setData($site);
        $filterform->handleRequest($request);

        //Mot-clef
        $nom = $filterform->get('nom')->getData();

        //Site organisateur
        $siteSelect = $filterform->get('site')->getData();
        $participantsSite = $participantRepository->findBy(['site' => $siteSelect]);

        //Etat
        $etat = $filterform->get('etat')->getData();

        //Between date de début et fin
        $dateDebut = $filterform->get('dateDebut')->getData();
        $dateFin = $filterform->get('dateFin')->getData();

        //Sorties dont je suis inscrit
        $inscrit = $filterform->get('estInscrit')->getData();

        //Sorties dont je ne suis pas inscrit
        $pasInscrit = $filterform->get('estPasInscrit')->getData();

        //Sortie dont je suis organisateur
        $sortieOrganisateur = $filterform->get('estOrganise')->getData();

        //Sorties passées
        $sortiePassee = $filterform->get('estPassee')->getData();

        if($filterform->isSubmitted() && $filterform->isValid()){
            return $this->render('sortie/list.html.twig', [
                'sorties' => $sortieRepository->findByFilters($nom, $siteSelect, $participantsSite, $etat, $dateDebut, $dateFin, $sortieOrganisateur, $inscrit, $pasInscrit, $sortiePassee, $user),
                'filterform' => $filterform
            ]);
        }

        return $this->render('sortie/list.html.twig', [
            'sorties' => $sortieRepository->findByFilters($nom, $siteSelect, $participantsSite, $etat, $dateDebut, $dateFin, $sortieOrganisateur, $inscrit, $pasInscrit, $sortiePassee, $user),
            'filterform' => $filterform
        ]);
    }

    #[Route('/creer', name: '_add')]
    public function add(Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository, VilleRepository $villeRepository, LieuRepository $lieuRepository, int $id = null): Response {
        $sortie =  new Sortie();
        $sortie->setOrganisateur($this->getUser());
        $sortie->setEtat(Etat::CREEE);

        $villes = $villeRepository->findAll();

        $form = $this->createForm(SortieFormType::class, $sortie);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $lieu = $lieuRepository->find($form->get('lieu')->getData() ?? -1);

            if($lieu){
                $sortie->setLieu($lieu);
                $entityManager->persist($sortie);
                $entityManager->flush();
                $this->addFlash('success', 'La sortie a été ajoutée avec succès !');
                return $this->redirectToRoute('sortie_list');
            }
            else {
                $form->get('lieu')->addError(new FormError("Vous devez sélectionner un lieu"));
            }
        }

        return $this->render('sortie/edit.html.twig', [
            'form' => $form,
            'action' => 'Créer',
            'villes' => $villes
        ]);
    }

    #[Route('/modifier/{id}', name: '_update', requirements: ['id' => '\d+'])]
    public function update(Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository, VilleRepository $villeRepository, LieuRepository $lieuRepository, int $id = null): Response
    {
        $sortie = $sortieRepository->find($id);
        $villes = $villeRepository->findAll();
        $user = $this->getUser();

        if(!$sortie || $sortie->getEtat()->name != 'CREEE' || (!in_array("ROLE_ADMIN", $user->getRoles()) && $user !== $sortie->getOrganisateur())) {
            return new Response("Vous n'êtes pas autorisé", 403);
        }

        $form = $this->createForm(SortieFormType::class, $sortie);
        $lieu = $lieuRepository->find($sortie->getLieu()->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $lieuSubmitted = $lieuRepository->find($form->get('lieu')->getData() ?? -1);
            if($lieuSubmitted) {
                $sortie->setLieu($lieuSubmitted);
                $entityManager->persist($sortie);
                $entityManager->flush();
                $this->addFlash('success', 'La sortie a été modifiée avec succès !');
                return $this->redirectToRoute('sortie_list');
            }
            else {
                $form->get('lieu')->addError(new FormError("Vous devez sélectionner un lieu"));
            }
        }

        return $this->render('sortie/edit.html.twig', [
            'form' => $form,
            'action' => 'Modifier',
            'villes' => $villes,
            'lieu' => $lieu
        ]);
    }

    #[Route('/details/{id}', name: '_detail', requirements: ['id' => '\d+'])]
    public function show(SortieRepository $sortieRepository, int $id = null): Response {

        $sortie = $sortieRepository->find($id);

        if(!$sortie || $sortie->getEtat()->name == 'CREEE') {
            return new Response("Vous n'êtes pas autorisé", 403);
        }

        $lieu = $sortie->getLieu();
        $ville = $lieu->getVille();
        $participants = $sortie->getParticipants();

        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
            'lieu' => $lieu,
            'ville' => $ville,
            'participants' => $participants
        ]);
    }

    #[Route('/inscrire/{id}', name: '_inscrire', requirements: ['id' => '\d+'])]
    public function register(EntityManagerInterface $entityManager, SortieRepository $sortieRepository, ParticipantRepository $participantRepository, UserInterface $user, int $id = null): Response {
        if ($id == null){
            $this->addFlash('danger', 'l\'indice de la sortie est null');
        } else {
            $sortie = $sortieRepository->find($id);
            $participant = $participantRepository->find($user->getId());
            $maintenant = new \DateTime();

            if ($sortie->getParticipants()->count() == $sortie->getNbInscriptionsMax()){
                $this->addFlash('danger', 'Le nombre max de participants a été atteint ! Vous ne pouvez pas vous inscrire...');
            } elseif ($sortie->getDateLimiteInscription()->format('Y-m-d H:i:s') < $maintenant->format('Y-m-d H:i:s')) {
                $this->addFlash('danger', 'La date limite pour les inscriptions est dépassée !');
            } elseif (($sortie->getEtat() != Etat::OUVERTE) and ($sortie->getEtat() != Etat::ENCOURS)) {
                $this->addFlash('danger', 'La sortie n\'est pas ouverte ou en cours, vous ne pouvez pas vous inscrire...');
            } else {
                $sortie->addParticipant($participant);
                $participant->addSortie($sortie);

                $entityManager->persist($sortie);
                $entityManager->persist($participant);

                if ($sortie->getParticipants()->count() == $sortie->getNbInscriptionsMax()){
                    $sortie->setEtat(Etat::CLOTUREE);
                    $entityManager->persist($sortie);
                }
                $entityManager->flush();
                $this->addFlash('success', 'Votre inscription a bien été prise en compte !');
            }
        }
        return $this->redirectToRoute('sortie_detail', ['id' => $id]);
    }

    #[Route('/desister/{id}', name: '_desister', requirements: ['id' => '\d+'])]
    public function withdraw(EntityManagerInterface $entityManager, SortieRepository $sortieRepository, ParticipantRepository $participantRepository, UserInterface $user, int $id = null): Response{
        if ($id == null){
            $this->addFlash('danger', 'l\'indice de la sortie est null');
        } else {
            $sortie = $sortieRepository->find($id);
            $participant = $participantRepository->find($user->getId());
            $participants = $sortie->getParticipants()->toArray();

            if (in_array($participant, $participants)) {
                $sortie->removeParticipant($participant);
                $participant->removeSortie($sortie);

                $entityManager->persist($sortie);
                $entityManager->persist($participant);
                $entityManager->flush();
                $this->addFlash('success', 'Votre désistement a bien été prise en compte !');
            } else {
                $this->addFlash('danger', 'Vous n\'êtes pas inscrits donc vous ne pouvez pas vous désister !');
            }
        }
        return $this->redirectToRoute('sortie_detail', ['id' => $id]);
    }

    #[Route('/publier/{id}', name: '_publish', requirements: ['id' => '\d+'])]
    public function publish(EntityManagerInterface $entityManager, SortieRepository $sortieRepository, int $id = null): Response{
        if ($id == null) {
            $this->addFlash('danger', 'l\'indice de la sortie est null');
        } else {
            $sortie = $sortieRepository->find($id);
            $maintenant = new \DateTime();
            if ($sortie->getDateLimiteInscription()->format('Y-m-d H:i:s') < $maintenant->format('Y-m-d H:i:s')) {
                $this->addFlash('danger', 'Vous ne pouvez pas publier votre sortie : la date de clôture est dépassée !');
            } elseif ($sortie->getEtat() != Etat::CREEE) {
                $this->addFlash('danger', 'Vous ne pouvez pas publier votre sortie : elle n\'est pas en cours de création!');
            } else {
                $sortie->setEtat(Etat::OUVERTE);
                $entityManager->persist($sortie);
                $entityManager->flush();
                $this->addFlash('success', 'La sortie a été publiée avec succès !');
            }
        }
        return $this->redirectToRoute('sortie_list');
    }


    #[Route('/supprimer/{id}', name: '_delete', requirements: ['id' => '\d+'])]
    public function delete(EntityManagerInterface $entityManager, Sortie $id = null): Response {
        $lieu = $id->getLieu();
        $lieu->removeSortie($id);

        $entityManager->remove($id);
        $entityManager->flush();
        $this->addFlash('success', 'La sortie a bien été supprimée !');

        return $this->redirectToRoute('sortie_list');
    }


    #[Route('/annuler/{id}', name: '_cancel', requirements: ['id' => '\d+'])]
    public function cancel(Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository, UserInterface $user, int $id = null): Response {

        $sortie = $sortieRepository->find($id);
        $site = $sortie->getOrganisateur()->getSite();
        $lieu = $sortie->getLieu();
        $form = $this->createForm(AnnulerSortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($sortie->getOrganisateur() !== $user ) {
                $this->addFlash('danger', 'Vous n\'êtes pas l\'organisateur de la sortie !');
            } elseif (($sortie->getEtat() != Etat::OUVERTE) and ($sortie->getEtat() != Etat::ENCOURS)) {
                $this->addFlash('danger', 'Vous ne pouvez pas annuler la sortie !');
            } else {
                $sortie->setEtat(Etat::ANNULEE);
                $entityManager->persist($sortie);
                $entityManager->flush();
                $this->addFlash('success', 'La sortie a été annulée avec succès !');
            }
            return $this->redirectToRoute('sortie_list');
        }
        return $this->render('sortie/cancel.html.twig', [
            'form' => $form,
            'sortie' => $sortie,
            'site' => $site,
            'lieu' => $lieu
        ]);
    }

}
