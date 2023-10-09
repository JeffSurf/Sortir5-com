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
class VilleController extends AbstractController
{
    #[Route('/list', name: '_list')]
    public function list(VilleRepository $repository): Response
    {
        $villes = $repository->findAll();

        return $this->render('ville/list.html.twig', [
            'villes' => $villes
        ]);
    }

    #[Route('/create', name: '_create')]
    public function create(Request $request, EntityManagerInterface $em): Response {
        $ville = new Ville();
        $villeFormulaire = $this->createForm(VilleType::class, $ville);

        $villeFormulaire->handleRequest($request);

        if($villeFormulaire->isSubmitted() && $villeFormulaire->isValid()){
            $em->persist($ville);
            $em->flush();

            $this->addFlash("success", "Ville ajoutée avec succès !");

            return $this->redirectToRoute('ville_list');
        }

        return $this->redirectToRoute('ville_create', [
            'form' => $villeFormulaire
        ]);
    }

    /*
    #[Route('/update/{id}', name: '_update', requirements: ['id' => '\d+'])]
    public function update(): Response {

    }

    #[Route('/delete/{id}', name: '_delete', requirements: ['id' => '\d+'])]
    public function delete():Response {

    }
    */
}
