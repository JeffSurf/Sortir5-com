<?php

namespace App\Controller\Admin;

use App\Entity\Ville;
use App\Form\SearchFormType;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/ville', name: 'app_admin_ville')]
class VilleController extends AbstractController {
    #[Route('', name: '_list')]
    public function lister(Request $request, EntityManagerInterface $entityManager, VilleRepository $villeRepository): Response {
        $villes = $villeRepository->findAll();

        //add
        $ville = new Ville();
        $addForm = $this->createForm(VilleType::class, $ville);
        $addForm->handleRequest($request);

        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $entityManager->persist($ville);
            $entityManager->flush();
            $this->addFlash('success', 'La ville a été ajoutée avec succès !');
            return $this->redirectToRoute('app_admin_ville_list');
        }

        //filter
        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);
        $searchValue = $searchForm->get('search')->getData();

        if ($searchForm->isSubmitted() && $searchForm->isValid() && $searchValue != '') {
            $villes = $villeRepository->filterByText($searchValue);
        }

        return $this->render('admin/ville/index.html.twig', [
            'villes' => $villes,
            'searchForm' => $searchForm,
            'addForm' => $addForm,
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
            return $this->redirectToRoute('app_admin_ville_list');
        }

        return $this->render('admin/ville/update.html.twig', [
            'form' => $form,
            'action' => 'Modifier'
        ]);
    }

    #[Route('/supprimer/{id}', name: '_delete', requirements: ['id' => '\d+'])]
    public function delete(EntityManagerInterface $entityManager, VilleRepository $villeRepository, int $id): Response {

        $ville = $villeRepository->find($id);

        if($ville->getLieux()->count() > 0)
            $this->addFlash('danger', 'La ville possède des lieux, vous ne pouvez pas la supprimer');
        else
        {
            $entityManager->remove($ville);
            $entityManager->flush();
            $this->addFlash('success', 'La ville a été supprimée avec succès !');
        }

        return $this->redirectToRoute('app_admin_ville_list');
    }
}