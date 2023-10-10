<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ville', name: 'ville')]
class VilleController extends AbstractController {
    #[Route('/', name: '_list')]
    public function lister(VilleRepository $villeRepository): Response {
        return $this->render('ville/index.html.twig', [
            'villes' => $villeRepository->findAll(),
        ]);
    }

    #[Route('/ajouter', name: '_add')]
    #[Route('/modifier/{id}', name: '_update')]
    public function edit(Request $request, EntityManagerInterface $entityManager,  VilleRepository $villeRepository, int $id = null): Response {

        $ville = $id == null ? new Ville() : $villeRepository->find($id);
        $msg = $ville->getId() == null ? 'La ville a été ajoutée avec succès !' : 'La ville a été modifiée avec succès !';
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ville);
            $entityManager->flush();
            $this->addFlash('success', $msg);
            return $this->redirectToRoute('ville_list');
        }

        return $this->render('ville/edit.html.twig', [
            'form' => $form,
            'action' => $ville->getId() == null ? 'Ajouter' : 'Modifier'
        ]);
    }

    #[Route('/supprimer/{id}', name: '_delete')]
    public function delete(EntityManagerInterface $entityManager, VilleRepository $villeRepository, int $id): Response {

        $ville = $villeRepository->find($id);
        $entityManager->remove($ville);
        $entityManager->flush();
        $this->addFlash('success', 'La ville a été supprimée avec succès !');
        return $this->redirectToRoute('ville_list');
    }
}