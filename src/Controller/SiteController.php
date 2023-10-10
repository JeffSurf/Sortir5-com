<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\SiteType;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/site', name: 'site')]
class SiteController extends AbstractController {
    #[Route('/', name: '_list')]
    public function lister(SiteRepository $siteRepository): Response {
        return $this->render('site/index.html.twig', [
            'sites' => $siteRepository->findAll(),
        ]);
    }

    #[Route('/ajouter', name: '_add')]
    #[Route('/modifier/{id}', name: '_update')]
    public function edit(Request $request, EntityManagerInterface $entityManager,  SiteRepository $siteRepository, int $id = null): Response {

        $site = $id == null ? new Site() : $siteRepository->find($id);
        $msg = $site->getId() == null ? 'Le site a été ajouté avec succès !' : 'Le site a été modifié avec succès !';
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($site);
            $entityManager->flush();
            $this->addFlash('success', $msg);
            return $this->redirectToRoute('site_list');
        }

        return $this->render('site/edit.html.twig', [
            'form' => $form,
            'action' => $site->getId() == null ? 'Ajouter' : 'Modifier'
        ]);
    }

    #[Route('/supprimer/{id}', name: '_delete')]
    public function delete(EntityManagerInterface $entityManager, SiteRepository $siteRepository, int $id): Response {

        $site = $siteRepository->find($id);
        $entityManager->remove($site);
        $entityManager->flush();
        $this->addFlash('success', 'Le site a été supprimé avec succès !');
        return $this->redirectToRoute('site_list');
    }
}