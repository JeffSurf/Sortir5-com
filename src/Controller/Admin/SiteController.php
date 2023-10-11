<?php

namespace App\Controller\Admin;

use App\Entity\Site;
use App\Form\SearchFormType;
use App\Form\SiteType;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/site', name: 'app_admin_site')]
class SiteController extends AbstractController {
    #[Route('', name: '_list')]
    public function lister(Request $request, EntityManagerInterface $entityManager, SiteRepository $siteRepository): Response {
        $sites = $siteRepository->findAll();

        //add
        $site = new Site();
        $addForm = $this->createForm(SiteType::class, $site);
        $addForm->handleRequest($request);

        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $entityManager->persist($site);
            $entityManager->flush();
            $this->addFlash('success', 'Le site a été ajouté avec succès !');
        }

        //filter
        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);
        $searchValue = $searchForm->get('search')->getData();

        if ($searchForm->isSubmitted() && $searchForm->isValid() && $searchValue != '') {
            $sites = $siteRepository->filterByText($searchValue);
        }

        return $this->render('site/index.html.twig', [
            'sites' => $sites,
            'searchForm' => $searchForm,
            'addForm' => $addForm,
        ]);
    }

    #[Route('/modifier/{id}', name: '_update', requirements: ['id' => '\d+'])]
    public function update(Request $request, EntityManagerInterface $entityManager, SiteRepository $siteRepository, int $id = null): Response {

        $site = $siteRepository->find($id);
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($site);
            $entityManager->flush();
            $this->addFlash('success', 'Le site a été modifié avec succès !');
            return $this->redirectToRoute('site_list');
        }

        return $this->render('site/update.html.twig', [
            'form' => $form,
            'action' => 'Modifier'
        ]);
    }

    #[Route('/supprimer/{id}', name: '_delete', requirements: ['id' => '\d+'])]
    public function delete(EntityManagerInterface $entityManager, SiteRepository $siteRepository, int $id): Response {

        $site = $siteRepository->find($id);

        if($site->getParticipants()->count() > 0)
            $this->addFlash('danger', 'Le site ' . $site->getNom() .' est rattaché à un ou plusieurs participants, vous ne pouvez pas le supprimer');
        else
        {
            $entityManager->remove($site);
            $entityManager->flush();
            $this->addFlash('success', 'Le site a été supprimé avec succès !');
        }

        return $this->redirectToRoute('site_list');
    }
}