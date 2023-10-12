<?php

namespace App\Controller\Admin;

use App\Entity\Lieu;
use App\Form\SearchFormType;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/lieu', name: 'app_admin_lieu')]
class LieuController extends AbstractController {
    #[Route('', name: '_list')]
    public function lister(Request $request, EntityManagerInterface $entityManager, LieuRepository $lieuRepository): Response {
        $lieux = $lieuRepository->findAll();

        //add
        $lieu = new Lieu();
        $addForm = $this->createForm(LieuType::class, $lieu);
        $addForm->handleRequest($request);

        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $entityManager->persist($lieu);
            $entityManager->flush();
            $this->addFlash('success', 'La lieu a été ajouté avec succès !');
            return $this->redirectToRoute('app_admin_lieu_list');
        }

        //filter
        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);
        $searchValue = $searchForm->get('search')->getData();

        if ($searchForm->isSubmitted() && $searchForm->isValid() && $searchValue != '') {
            $lieux = $lieuRepository->filterByText($searchValue);
        }

        return $this->render('admin/lieu/index.html.twig', [
            'lieux' => $lieux,
            'searchForm' => $searchForm,
            'addForm' => $addForm,
        ]);
    }

    #[Route('/modifier/{id}', name: '_update', requirements: ['id' => '\d+'])]
    public function update(Request $request, EntityManagerInterface $entityManager, LieuRepository $lieuRepository, int $id = null): Response {

        $lieu = $lieuRepository->find($id);
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lieu);
            $entityManager->flush();
            $this->addFlash('success', 'Le lieu a été modifié avec succès !');
            return $this->redirectToRoute('app_admin_lieu_list');
        }

        return $this->render('admin/lieu/update.html.twig', [
            'form' => $form,
            'action' => 'Modifier'
        ]);
    }

    #[Route('/supprimer/{id}', name: '_delete', requirements: ['id' => '\d+'])]
    public function delete(EntityManagerInterface $entityManager, LieuRepository $lieuRepository, int $id): Response {

        $lieu = $lieuRepository->find($id);

        if($lieu->getSorties()->count() > 0) {
            $this->addFlash('danger', 'Ce lieu ' . $lieu->getNom() .' est rattaché à ' . $lieu->getSorties()->count() . ' sortie' . $lieu->getSorties()->count()>1 ? 's' : '' . ', vous ne pouvez pas le supprimer');
        } else {
            $entityManager->remove($lieu);
            $entityManager->flush();
            $this->addFlash('success', 'Le lieu a été supprimé avec succès !');
        }

        return $this->redirectToRoute('app_admin_lieu_list');
    }
}