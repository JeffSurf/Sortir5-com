<?php

namespace App\Entity;

use App\Repository\HistoriqueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueRepository::class)]
class Historique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom_sortie = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_heure_debut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateLimiteInscription = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $infosSortie = null;

    #[ORM\Column(type: "string", enumType: Etat::class)]
    private ?Etat $etat = null;

    #[ORM\Column(length: 50)]
    private ?string $nomLieu = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 50)]
    private ?string $nomVille = null;

    #[ORM\Column(length: 5)]
    private ?string $codePostal = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motifAnnulation = null;

    #[ORM\Column(length: 50)]
    private ?string $nomOrganisateur = null;

    #[ORM\Column(length: 50)]
    private ?string $prenomOrganisateur = null;

    #[ORM\Column(length: 255)]
    private ?string $nomSiteOrganisateur = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $telephoneOrganisateur = null;

    #[ORM\Column(length: 50)]
    private ?string $nomParticipant = null;

    #[ORM\Column(length: 50)]
    private ?string $prenomParticipant = null;

    #[ORM\Column(length: 255)]
    private ?string $nomSiteParticipant = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $telephoneParticipant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomSortie(): ?string
    {
        return $this->nom_sortie;
    }

    public function setNomSortie(string $nom_sortie): static
    {
        $this->nom_sortie = $nom_sortie;

        return $this;
    }

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->date_heure_debut;
    }

    public function setDateHeureDebut(\DateTimeInterface $date_heure_debut): static
    {
        $this->date_heure_debut = $date_heure_debut;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(\DateTimeInterface $dateLimiteInscription): static
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(?string $infosSortie): static
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }

    public function getEtat(): Etat
    {
        return $this->etat;
    }

    public function setEtat(Etat $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getNomLieu(): ?string
    {
        return $this->nomLieu;
    }

    public function setNomLieu(string $nomLieu): static
    {
        $this->nomLieu = $nomLieu;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getNomVille(): ?string
    {
        return $this->nomVille;
    }

    public function setNomVille(string $nomVille): static
    {
        $this->nomVille = $nomVille;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): static
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getMotifAnnulation(): ?string
    {
        return $this->motifAnnulation;
    }

    public function setMotifAnnulation(?string $motifAnnulation): static
    {
        $this->motifAnnulation = $motifAnnulation;

        return $this;
    }

    public function getNomOrganisateur(): ?string
    {
        return $this->nomOrganisateur;
    }

    public function setNomOrganisateur(string $nomOrganisateur): static
    {
        $this->nomOrganisateur = $nomOrganisateur;

        return $this;
    }

    public function getPrenomOrganisateur(): ?string
    {
        return $this->prenomOrganisateur;
    }

    public function setPrenomOrganisateur(string $prenomOrganisateur): static
    {
        $this->prenomOrganisateur = $prenomOrganisateur;

        return $this;
    }

    public function getNomSiteOrganisateur(): ?string
    {
        return $this->nomSiteOrganisateur;
    }

    public function setNomSiteOrganisateur(string $nomSiteOrganisateur): static
    {
        $this->nomSiteOrganisateur = $nomSiteOrganisateur;

        return $this;
    }

    public function getTelephoneOrganisateur(): ?string
    {
        return $this->telephoneOrganisateur;
    }

    public function setTelephoneOrganisateur(?string $telephoneOrganisateur): static
    {
        $this->telephoneOrganisateur = $telephoneOrganisateur;

        return $this;
    }

    public function getNomParticipant(): ?string
    {
        return $this->nomParticipant;
    }

    public function setNomParticipant(string $nomParticipant): static
    {
        $this->nomParticipant = $nomParticipant;

        return $this;
    }

    public function getPrenomParticipant(): ?string
    {
        return $this->prenomParticipant;
    }

    public function setPrenomParticipant(string $prenomParticipant): static
    {
        $this->prenomParticipant = $prenomParticipant;

        return $this;
    }

    public function getNomSiteParticipant(): ?string
    {
        return $this->nomSiteParticipant;
    }

    public function setNomSiteParticipant(string $nomSiteParticipant): static
    {
        $this->nomSiteParticipant = $nomSiteParticipant;

        return $this;
    }

    public function getTelephoneParticipant(): ?string
    {
        return $this->telephoneParticipant;
    }

    public function setTelephoneParticipant(?string $telephoneParticipant): static
    {
        $this->telephoneParticipant = $telephoneParticipant;

        return $this;
    }
}
