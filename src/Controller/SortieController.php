<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\SortieFormType;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/sortie', name: 'sortie')]
class SortieController extends AbstractController
{
    #[Route('', name: '_list')]
    public function list(Request $request, SortieRepository $sortieRepository, SiteRepository $siteRepository): Response
    {
        $name = $request->get('keywords');
        $etat = $request->get('etat');

        $etats = Etat::cases();
        return $this->render('sortie/list.html.twig', [
            'sorties' => $sortieRepository->findByFilters($name, $etat),
            'sites' => $siteRepository->findAll(),
            'etats' => $etats
        ]);
    }

    #[Route('/ajouter', name: '_add')]
    #[Route('/modifier/{id}', name: '_update', requirements: ['id' => '\d+'])]
    #[Route('/afficher/{id}', name: '_detail', requirements: ['id' => '\d+'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository, int $id = null): Response {
        $sortie = $id == null ? new Sortie() : $sortieRepository->find($id);
        $sortie->setOrganisateur($this->getUser());
        $sortie->setEtat(Etat::CREEE);
        $msg = $sortie->getId() == null ? 'La sortie a été ajoutée avec succès !' : 'La sortie a été modifiée avec succès !';

        $form = $this->createForm(SortieFormType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //conditions de formulaire !!!
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', $msg);
            return $this->redirectToRoute('sortie_list');
        }

        return $this->render('sortie/edit.html.twig', [
            'form' => $form,
            'action' => $sortie->getId() == null ? 'Ajouter' : 'Modifier'
        ]);
    }

    #[Route('/inscrire/{id}', name: '_inscrire', requirements: ['id' => '\d+'])]
    public function SignUp(EntityManagerInterface $entityManager, SortieRepository $sortieRepository, ParticipantRepository $participantRepository, int $id = null, UserInterface $user): Response {
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
        return $this->redirectToRoute('sortie_list');
    }

    #[Route('/desister/{id}', name: '_desister', requirements: ['id' => '\d+'])]
    public function desister(EntityManagerInterface $entityManager, SortieRepository $sortieRepository, ParticipantRepository $participantRepository, UserInterface $user, int $id = null): Response{
        if ($id == null){
            $this->addFlash('danger', 'l\'indice de la sortie est null');
        } else {
            $sortie = $sortieRepository->find($id);
            $participant = $participantRepository->find($user->getId());

            foreach ($sortie->getParticipants() as $participantSortie){
                if ($participantSortie === $participant) {
                    $sortie->removeParticipant($participant);
                    $participant->removeSortie($sortie);

                    $entityManager->persist($sortie);
                    $entityManager->persist($participant);
                    $entityManager->flush();
                    $this->addFlash('success', 'Votre désistement a bien été prise en compte !');
                    break;
                } else {
                    $this->addFlash('danger', 'Vous n\'êtes pas inscrits donc vous ne pouvez pas vous désister !');
                }
            }
        }
        return $this->redirectToRoute('sortie_list');
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


    #[Route('/annuler/{id}', name: '_cancel', requirements: ['id' => '\d+'])]
    public function cancel(EntityManagerInterface $entityManager, SortieRepository $sortieRepository, UserInterface $user, int $id = null): Response {
        if ($id == null){
            $this->addFlash('danger', 'l\'indice de la sortie est null');
        } else {
            $sortie = $sortieRepository->find($id);
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
        }
        return $this->redirectToRoute('sortie_list');
    }

}
