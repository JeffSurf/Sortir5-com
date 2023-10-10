<?php

namespace App\DataFixtures;

use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
;

class VilleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $villes = [
            ['nom' => 'Paris', 'codePostal' => '75001'],
            ['nom' => 'Marseille', 'codePostal' => '13001'],
            ['nom' => 'Lyon', 'codePostal' => '69001'],
            ['nom' => 'Toulouse', 'codePostal' => '31000'],
            ['nom' => 'Nice', 'codePostal' => '06000'],
            ['nom' => 'Nantes', 'codePostal' => '44000'],
            ['nom' => 'Strasbourg', 'codePostal' => '67000'],
            ['nom' => 'Montpellier', 'codePostal' => '34000'],
            ['nom' => 'Bordeaux', 'codePostal' => '33000'],
            ['nom' => 'Lille', 'codePostal' => '59000']
        ];

        foreach ($villes as $ville) {
            $city = new Ville();
            $city->setNom($ville['nom']);
            $city->setCodePostal($ville['codePostal']);
            $manager->persist($city);
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
