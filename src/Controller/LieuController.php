<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/lieu', name: 'lieu')]
class LieuController extends AbstractController
{
    #[Route(name: '_create', methods: "POST")]
    public function create(
        Request $request,
        VilleRepository $villeRepository,
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        SerializerInterface $serializer
    ): Response {
        $nom = $request->request->get('lieu_nom');
        $adresse = $request->request->get('lieu_adresse');
        $lat = $request->request->get('lieu_latitude');
        $lon = $request->request->get('lieu_longitude');
        $villeId = $request->request->get('lieu_ville');

        $ville = $villeRepository->find($villeId);

        $lieu = new Lieu();
        $lieu->setNom($nom)
            ->setAdresse($adresse)
            ->setVille($ville);

        if($lat && $lon) {
            $lieu->setLongitude($lon)
                ->setLatitude($lat);
        }

        $errors = $validator->validate($lieu);

        if($errors->count() > 0) {
            $error_json = array();
            foreach ($errors as $error) {
                $error_json[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse($error_json, 400);
        }

        $em->persist($lieu);
        $em->flush();

        return new JsonResponse($serializer->serialize($lieu, "json"));

    }
}
