<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
;

class GVFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        //4 utilisateurs dont un admin
        //5 villes
        //2-4 lieux par villes
        //4 sites
        //1-3 sorties : 1 pour un orga, 1 avec 1 en nbmax + 1 sortie chaque état
        //NE pas oublier de le manytomany entre Sortie et Participant

        $manager->flush();
    }
}
