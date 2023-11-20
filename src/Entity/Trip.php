<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\TripRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
 
#[ApiResource]
#[ORM\Entity(repositoryClass: TripRepository::class)]
class Trip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $prix = null;

    #[ORM\Column]
    private ?bool $fumeur = null;

    #[ORM\Column]
    private ?bool $silence = null;

    #[ORM\Column]
    private ?bool $musique = null;

    #[ORM\Column]
    private ?bool $animaux = null;

    #[ORM\Column]
    private ?string $date_depart = null;

    #[ORM\Column]
    private ?string $heure_depart = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Car $car = null;
   
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'trip', targetEntity: Etape::class, orphanRemoval: true)]
    private Collection $etapes;

    public function __construct()
    {
        $this->etapes = new ArrayCollection();
    }
    

    public function getEtapes(): Collection
    {
        return $this->etapes;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function isFumeur(): ?bool
    {
        return $this->fumeur;
    }

    public function setFumeur(bool $fumeur): static
    {
        $this->fumeur = $fumeur;

        return $this;
    }

    public function isSilence(): ?bool
    {
        return $this->silence;
    }

    public function setSilence(bool $silence): static
    {
        $this->silence = $silence;

        return $this;
    }

    public function isMusique(): ?bool
    {
        return $this->musique;
    }

    public function setMusique(bool $musique): static
    {
        $this->musique = $musique;

        return $this;
    }

    public function isAnimaux(): ?bool
    {
        return $this->animaux;
    }

    public function setAnimaux(bool $animaux): static
    {
        $this->animaux = $animaux;

        return $this;
    }

    public function getDateDepart(): ?string
    {
        return $this->date_depart;
    }

    public function setDateDepart(string $date_depart): static
    {
        $this->date_depart = $date_depart;

        return $this;
    }

    public function getHeureDepart(): ?string
    {
        return $this->heure_depart;
    }

    public function setHeureDepart(string $heure_depart): static
    {
        $this->heure_depart = $heure_depart;

        return $this;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): static
    {
        $this->car = $car;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
{
    $this->user = $user;

    return $this;
}

public function addEtape(Etape $etape): self
{
    if (!$this->etapes->contains($etape)) {
        $this->etapes[] = $etape;
        $etape->setTrip($this);
    }

    return $this;
}


}