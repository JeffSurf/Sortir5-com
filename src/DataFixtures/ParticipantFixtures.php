<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use App\Entity\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
;

class ParticipantFixtures extends Fixture
{

    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {

        $site = new Site();
        $site->setNom('CAMPUS TEST PARTICIPANT');
        $manager->persist($site);
        $manager->flush();

        for ($i = 0; $i < 10; $i++) {
            $participant = new Participant();
            $participant->setPseudo('user' . $i);
            $participant->setEmail('user' . $i . '@sortir.com');
            $participant->setNom('Nom' . $i);
            $participant->setPrenom('Prenom' . $i);
            $participant->setRoles(['ROLE_USER']);
            $participant->setTelephone('06 06 06 06 0' . $i);
            $participant->setActif(true);
            $password = $this->passwordHasher->hashPassword($participant, 'sortir' . $i);
            $participant->setPassword($password);

            $participant->setSite($site);

            $manager->persist($participant);
        }

        $manager->flush();

    }

}
