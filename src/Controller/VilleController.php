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
    public function lister(Request $request, EntityManagerInterface $entityManager, VilleRepository $villeRepository): Response {

        //add
        $ville = new Ville();
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ville);
            $entityManager->flush();
            $this->addFlash('success', 'La ville a été ajoutée avec succès !');
            return $this->redirectToRoute('ville_list');
        }

        return $this->render('ville/index.html.twig', [
            'villes' => $villeRepository->findAll(),
            'form' => $form
        ]);
    }

    #[Route('/modifier/{id}', name: '_update', requirements: ['id' => '\d+'])]
    public function update(Request $request, EntityManagerInterface $entityManager, VilleRepository $villeRepository, int $id = null): Response {

        $ville = $villeRepository->find($id);
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ville);
            $entityManager->flush();
            $this->addFlash('success', 'La ville a été modifiée avec succès !');
            return $this->redirectToRoute('ville_list');
        }

        return $this->render('ville/update.html.twig', [
            'form' => $form,
            'action' => 'Modifier'
        ]);
    }

    #[Route('/supprimer/{id}', name: '_delete', requirements: ['id' => '\d+'])]
    public function delete(EntityManagerInterface $entityManager, VilleRepository $villeRepository, int $id): Response {

        $ville = $villeRepository->find($id);
        $entityManager->remove($ville);
        $entityManager->flush();
        $this->addFlash('success', 'La ville a été supprimée avec succès !');
        return $this->redirectToRoute('ville_list');
    }
}