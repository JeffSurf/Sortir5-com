<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
;

class GVFixtures extends Fixture
{

    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {

        $site1 = new Site();
        $site1->setNom('Campus Niort');
        $manager->persist($site1);

        $site2 = new Site();
        $site2->setNom('Campus Nantes');
        $manager->persist($site2);

        $site3 = new Site();
        $site3->setNom('Campus La Roche sur Yon');
        $manager->persist($site3);

        $site4 = new Site();
        $site4->setNom('Campus La Rochelle');
        $manager->persist($site4);

        $ville1 = new Ville();
        $ville1->setNom('Nantes');
        $ville1->setCodepostal('44000');
        $manager->persist($ville1);

        $ville2 = new Ville();
        $ville2->setNom('Niort');
        $ville2->setCodepostal('79000');
        $manager->persist($ville2);

        $ville3 = new Ville();
        $ville3->setNom('La Roche sur Yon');
        $ville3->setCodepostal('85000');
        $manager->persist($ville3);

        $lieu1 = new Lieu();
        $lieu1->setNom('Lamacotte');
        $lieu1->setAdresse('7 Rue Saint-Denis');
        $lieu1->setVille($ville1);
        $manager->persist($lieu1);

        $lieu2 = new Lieu();
        $lieu2->setNom('UGC Cinéma');
        $lieu2->setAdresse('Pl. Jean-Bart');
        $lieu2->setVille($ville1);
        $manager->persist($lieu2);

        $lieu3 = new Lieu();
        $lieu3->setNom('Restaurant du Donjon');
        $lieu3->setAdresse('7 Rue Brisson');
        $lieu3->setVille($ville2);
        $manager->persist($lieu3);

        $lieu4 = new Lieu();
        $lieu4->setNom('CGR Cinéma');
        $lieu4->setAdresse('Esp. du Jardin de la Brèche');
        $lieu4->setVille($ville2);
        $manager->persist($lieu4);

        $lieu5 = new Lieu();
        $lieu5->setNom('Restaurant Le Karo');
        $lieu5->setAdresse('Carreau des Halles');
        $lieu5->setVille($ville3);
        $manager->persist($lieu5);

        $lieu6 = new Lieu();
        $lieu6->setNom('Cinéville');
        $lieu6->setAdresse('Rue François Cevert');
        $lieu6->setVille($ville3);
        $manager->persist($lieu6);


        $participant1 = new Participant();
        $participant1->setPseudo('gvincent');
        $participant1->setNom('VINCENT');
        $participant1->setPrenom('Guillaume');
        $participant1->setEmail('gvincent@sortir.com');
        $participant1->setRoles(['ROLE_USER']);
        $participant1->setTelephone(('0606060601'));
        $password1 = $this->passwordHasher->hashPassword($participant1, 'GVSortir.com1');
        $participant1->setPassword($password1);
        $participant1->setSite($site1);
        $manager->persist($participant1);

        $participant2 = new Participant();
        $participant2->setPseudo('vsailly');
        $participant2->setNom('SAILLY');
        $participant2->setPrenom('Vincent');
        $participant2->setEmail('vsailly@sortir.com');
        $participant2->setRoles(['ROLE_USER']);
        $participant2->setTelephone(('0606060602'));
        $password2 = $this->passwordHasher->hashPassword($participant2, 'VSSortir.com1');
        $participant2->setPassword($password2);
        $participant2->setSite($site1);
        $manager->persist($participant2);

        $participant3 = new Participant();
        $participant3->setPseudo('ssebbaghi');
        $participant3->setNom('SEBBAGHI');
        $participant3->setPrenom('Sofiane');
        $participant3->setEmail('ssebbaghi@sortir.com');
        $participant3->setRoles(['ROLE_USER']);
        $participant3->setTelephone(('0606060603'));
        $password3 = $this->passwordHasher->hashPassword($participant3, 'SSSortir.com1');
        $participant3->setPassword($password3);
        $participant3->setSite($site1);
        $manager->persist($participant3);

        $participant4 = new Participant();
        $participant4->setPseudo('adminsortir');
        $participant4->setNom('ADMIN');
        $participant4->setPrenom('Admin');
        $participant4->setEmail('admin@sortir.com');
        $participant4->setRoles(['ROLE_ADMIN']);
        $participant4->setTelephone(('0606060604'));
        $password4 = $this->passwordHasher->hashPassword($participant4, 'ADSortir.com1');
        $participant4->setPassword($password4);
        $participant4->setSite($site1);
        $manager->persist($participant4);

        $sortie1 = new Sortie();
        $sortie1->setNom('Ciné entre étudiants');
        $sortie1->setDateHeureDebut(new \DateTime('2023-10-31 00:00:00'));
        $sortie1->setDateLimiteInscription(new \DateTime('2023-10-30 00:00:00'));
        $sortie1->setNbInscriptionsMax(10);
        $sortie1->setInfosSortie('Cohésion');
        $sortie1->setOrganisateur($participant1);
        $sortie1->setLieu($lieu4);
        $sortie1->setEtat(Etat::CREEE);
        $manager->persist($sortie1);

        $sortie2 = new Sortie();
        $sortie2->setNom('Restaurant');
        $sortie2->setDateHeureDebut(new \DateTime('2023-10-31 00:00:00'));
        $sortie2->setDateLimiteInscription(new \DateTime('2023-10-30 00:00:00'));
        $sortie2->setNbInscriptionsMax(10);
        $sortie2->setInfosSortie('Faire connaissance !');
        $sortie2->setOrganisateur($participant2);
        $sortie2->setLieu($lieu3);
        $sortie2->setEtat(Etat::OUVERTE);
        $sortie2->addParticipant($participant1);
        $manager->persist($sortie2);

        $participant1->addSortie($sortie2);

        $sortie3 = new Sortie();
        $sortie3->setNom('Restaurant Nantes');
        $sortie3->setDateHeureDebut(new \DateTime('2023-10-31 00:00:00'));
        $sortie3->setDateLimiteInscription(new \DateTime('2023-10-30 00:00:00'));
        $sortie3->setNbInscriptionsMax(2);
        $sortie3->setInfosSortie('Faire connaissance !');
        $sortie3->setOrganisateur($participant1);
        $sortie3->setLieu($lieu1);
        $sortie3->setEtat(Etat::OUVERTE);
        $manager->persist($sortie3);



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
