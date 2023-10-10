<?php

namespace App\Entity;

use App\Repository\InscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InscriptionRepository::class)]
class Inscription
{

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participant $Participant = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sortie $Sortie = null;

    public function getParticipant(): ?Participant
    {
        return $this->Participant;
    }

    public function setParticipant(?Participant $Participant): static
    {
        $this->Participant = $Participant;

        return $this;
    }

    public function getSortie(): ?Sortie
    {
        return $this->Sortie;
    }

    public function setSortie(?Sortie $Sortie): static
    {
        $this->Sortie = $Sortie;

        return $this;
    }
}
