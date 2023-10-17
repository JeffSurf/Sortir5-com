<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'Il y a déjà un compte avec cet email')]
#[UniqueEntity(fields: ['pseudo'], message: 'Il y a déjà un compte avec ce pseudo')]
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Assert\NotBlank(message: "L'email ne peut pas être vide"),]
    #[Assert\Email(message: "Le format n'est pas valide (exemple@sortir.com"),]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var ?string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide")]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le prénom ne peut pas être vide")]
    private ?string $prenom = null;

    #[ORM\Column(length: 15, nullable: true)]
    #[Assert\Length(exactly: 10, exactMessage: "Le numéro de téléphone doit faire {{ limit }} caractères")]
    #[Assert\Regex(
        pattern: "/^0[1-7][0-9]+/",
        message: "Le numéro de téléphone doit commencer par 01 jusqu'à 07",
        match: true
    )]
    private ?string $telephone = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank(message: "Le pseudo ne peut pas être vide")]
    #[Assert\Length(min: 2, minMessage: "Le pseudo doit contenir au minimum {{ limit }} caractères")]
    #[Assert\Regex(
        pattern: '/^([a-zA-Z]|[0-9]|_)+$/',
        message: 'Le pseudo peut contenir des lettres (a à z), des chiffres et _',
        match: true,
    )]
    private ?string $pseudo = null;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

    #[ORM\OneToMany(mappedBy: 'organisateur', targetEntity: Sortie::class, orphanRemoval: true)]
    private Collection $sortiesOrganisateur;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageProfil = 'default_profile_picture.png';

    #[ORM\ManyToMany(targetEntity: Sortie::class, inversedBy: 'participants')]
    private Collection $sorties;

    #[ORM\Column]
    private ?bool $rgpd = false;

    public function __construct()
    {
        $this->sortiesOrganisateur = new ArrayCollection();
        $this->sorties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER

        if(!in_array('ROLE_BAN', $roles))
            $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesOrganisateur(): Collection
    {
        return $this->sortiesOrganisateur;
    }

    public function addSortieOrganisateur(Sortie $sortie): static
    {
        if (!$this->sortiesOrganisateur->contains($sortie)) {
            $this->sortiesOrganisateur->add($sortie);
            $sortie->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortieOrganisateur(Sortie $sortie): static
    {
        if ($this->sortiesOrganisateur->removeElement($sortie)) {
            // set the owning side to null (unless already changed)
            if ($sortie->getOrganisateur() === $this) {
                $sortie->setOrganisateur(null);
            }
        }

        return $this;
    }

    public function getImageProfil(): ?string
    {
        return $this->imageProfil;
    }

    public function setImageProfil(?string $imageProfil): static
    {
        $this->imageProfil = $imageProfil;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSortie(Sortie $sortie): static
    {
        if (!$this->sorties->contains($sortie)) {
            $this->sorties->add($sortie);
        }

        return $this;
    }

    public function removeSortie(Sortie $sortie): static
    {
        $this->sorties->removeElement($sortie);

        return $this;
    }

    #[ORM\PreRemove]
    public function removeAllSorties() : void
    {
        /** @var Sortie $sortie */
        foreach ($this->sorties as $sortie)
        {
            $sortie->removeParticipant($this);
        }
    }

    public function isRgpd(): ?bool
    {
        return $this->rgpd;
    }

    public function setRgpd(bool $rgpd): static
    {
        $this->rgpd = $rgpd;

        return $this;
    }
}
