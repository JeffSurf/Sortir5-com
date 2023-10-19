<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {/*
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

        $villes = [
            ['nom' => 'Nantes', 'codePostal' => '44000'],
            ['nom' => 'Angers', 'codePostal' => '49000'],
            ['nom' => 'La Roche sur Yon', 'codePostal' => '85000'],
            ['nom' => 'La Rochelle', 'codePostal' => '17000']
        ];

        foreach ($villes as $ville) {
            $newVille = new Ville();
            $newVille->setNom($ville['nom']);
            $newVille->setCodePostal($ville['codePostal']);
            $manager->persist($newVille);

            $lieux = [
                ['nom' => 'Restaurant'],
                ['nom' => 'Cinéma'],
                ['nom' => 'Bar']
            ];

            foreach ($lieux as $lieu) {
                $newLieu = new Lieu();
                $newLieu->setNom($lieu['nom']);
                $newLieu->setVille($newVille);
                $manager->persist($newLieu);
            }
        }

        $site = new Site();
        $site->setNom('CAMPUS TEST PARTICIPANT');
        $manager->persist($site);

        for ($i = 0; $i < 10; $i++) {
            $participant = new Participant();
            $participant->setPseudo('user' . $i);
            $participant->setEmail('user' . $i . '@sortir.com');
            $participant->setNom('Nom' . $i);
            $participant->setPrenom('Prenom' . $i);
            $participant->setRoles(['ROLE_USER']);
            $participant->setTelephone('060606060' . $i);
            $password = $this->passwordHasher->hashPassword($participant, 'sortir' . $i);
            $participant->setPassword($password);

            $participant->setSite($site);

            $manager->persist($participant);
        }

        $sorties = [
            [
                'nom'=>'Faire connaissance !',
                'dateHeureDebut'=> new \DateTime('now'),
                'dateLimiteInscritpion' => new \DateTime('now'),
                'nbInscriptionsMax' => 20,
                'infosSortie'=> 'Teambuilding entre collègues !'
            ],
            [
                'nom'=>'Apprendre à connaître ses collègues !',
                'dateHeureDebut'=> new \DateTime('now'),
                'dateLimiteInscritpion' => new \DateTime('now'),
                'nbInscriptionsMax' => 30,
                'infosSortie'=> 'Cohésion entre collègues !'
            ]
        ];

        $newVille = new Ville();
        $newVille->setNom('Niort')->setCodepostal('79000');
        $manager->persist($newVille);

        $newLieu = new Lieu();
        $newLieu->setNom('Bowling')
            ->setVille($newVille);
        $manager->persist($newLieu);

        foreach ($sorties as $sortie){
            $newSortie = new Sortie();
            $newSortie->setNom($sortie['nom']);
            $newSortie->setDateHeureDebut($sortie['dateHeureDebut']);
            $newSortie->setDateLimiteInscription($sortie['dateLimiteInscritpion']);
            $newSortie->setNbInscriptionsMax($sortie['nbInscriptionsMax']);
            $newSortie->setInfosSortie($sortie['infosSortie']);
            $newSortie->setLieu($newLieu);
            $newSortie->setOrganisateur($participant);
            $manager->persist($newSortie);
        }
        */
        $manager->flush();
    }
}
