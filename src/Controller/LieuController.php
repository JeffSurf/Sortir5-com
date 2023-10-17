<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/lieu', name: 'lieu')]
class LieuController extends AbstractController
{
    #[Route(name: '_create', methods: "POST")]
    public function create(Request $request, VilleRepository $villeRepository, ValidatorInterface $validator, LoggerInterface $logger): Response {
        $nom = $request->request->get('lieu_nom');
        $adresse = $request->request->get('lieu_adresse');
        $lat = $request->request->get('lieu_lat');
        $lon = $request->request->get('lieu_lon');
        $villeId = $request->request->get('lieu_ville');

        $ville = $villeRepository->find($villeId);

        $lieu = new Lieu();
        $lieu->setNom($nom)
            ->setAdresse($adresse)
            ->setVille($ville);

        if($lat && $lon && is_float($lat) && is_float($lon)) {
            $lieu->setLongitude($lon)
                ->setLatitude($lat);
        }

        $errors = $validator->validate($lieu);

        if($errors->count() > 0)
        {
            $error_json = array();
            foreach ($errors as $error) {
                $error_json[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse($error_json, 400);
        }

        return new Response('Réussite');

    }
}
