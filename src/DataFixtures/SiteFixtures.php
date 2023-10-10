<?php

namespace App\DataFixtures;

use App\Entity\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
;

class SiteFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $sites = [
            ['nom' => 'CAMPUS NIORT'],
            ['nom' => 'CAMPUS NANTES'],
            ['nom' => 'CAMPUS LRY'],
            ['nom' => 'CAMPUS ANGERS']
        ];

        foreach ($sites as $site) {
            $newSite = new Site();
            $newSite->setNom($site['nom']);
            $manager->persist($newSite);
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
