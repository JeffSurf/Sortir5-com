<?php

namespace App\Service;

use App\Entity\Historique;
use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;

class ParticipantService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function archive(Participant $participant, Sortie $sortie) : void {
        $historique = new Historique();

        $historique
            ->setNomSortie($sortie->getNom())
            ->setDateHeureDebut($sortie->getDateHeureDebut())
            ->setDateLimiteInscription($sortie->getDateLimiteInscription())
            ->setInfosSortie($sortie->getInfosSortie())
            ->setEtat($sortie->getEtat())
            ->setNomOrganisateur($sortie->getOrganisateur()->getNom())
            ->setPrenomOrganisateur($sortie->getOrganisateur()->getPrenom())
            ->setTelephoneOrganisateur($sortie->getOrganisateur()->getTelephone())
            ->setNomSiteOrganisateur($sortie->getOrganisateur()->getSite()->getNom())
            ->setNomLieu($sortie->getLieu()->getNom())
            ->setAdresse($sortie->getLieu()->getAdresse())
            ->setMotifAnnulation($sortie->getMotifAnnulation())
            ->setCodePostal($sortie->getLieu()->getVille()->getCodepostal())
            ->setNomVille($sortie->getLieu()->getVille()->getNom())
            ->setNomParticipant($participant->getNom())
            ->setPrenomParticipant($participant->getPrenom())
            ->setTelephoneParticipant($participant->getTelephone())
            ->setNomSiteParticipant($participant->getSite()->getNom())
        ;

        $this->em->persist($historique);
        $this->em->flush();
    }
}