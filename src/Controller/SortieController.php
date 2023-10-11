<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\SortieFormType;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'sortie')]
class SortieController extends AbstractController
{
    #[Route('', name: '_list')]
    public function list(SortieRepository $sortieRepository, SiteRepository $siteRepository): Response
    {
        $etats = Etat::cases();
        return $this->render('sortie/list.html.twig', [
            'sorties' => $sortieRepository->findAll(),
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

    #[Route('/publier/{id}', name: '_publish', requirements: ['id' => '\d+'])]
    public function publish(EntityManagerInterface $entityManager, SortieRepository $sortieRepository, int $id = null){
        if ($id == null){
            $this->addFlash('danger', 'l\'indice de la sortie est null');
        } else {
            $sortie = $sortieRepository->find($id);
            $sortie->setEtat(Etat::OUVERTE);
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', 'La sortie a été publiée avec succès !');
        }
        return $this->redirectToRoute('sortie_list');
    }


    #[Route('/annuler/{id}', name: '_cancel', requirements: ['id' => '\d+'])]
    public function cancel(EntityManagerInterface $entityManager, SortieRepository $sortieRepository, int $id = null): Response {
        if ($id == null){
            $this->addFlash('danger', 'l\'indice de la sortie est null');
        } else {
            $sortie = $sortieRepository->find($id);
            $sortie->setEtat(Etat::ANNULEE);
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', 'La sortie a été annulée avec succès !');
        }
        return $this->redirectToRoute('sortie_list');
    }

}
