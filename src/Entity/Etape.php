<?php

namespace App\Entity;

use App\Repository\EtapeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
 
#[ApiResource]
#[ORM\Entity(repositoryClass: EtapeRepository::class)]
class Etape
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse_depart = null;

    #[ORM\Column]
    private ?int $code_postal_depart = null;

    #[ORM\Column(length: 255)]
    private ?string $ville_depart = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse_arrivee = null;

    #[ORM\Column]
    private ?int $code_postal_arrivee = null;

    #[ORM\Column(length: 255)]
    private ?string $ville_arrivee = null;

   

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trip $trip = null;

    public function getId(): ?int
    {
        return $this->id;
    }

  
    public function getAdresseDepart(): ?string
    {
        return $this->adresse_depart;
    }

    public function setAdresseDepart(string $adresse_depart): static
    {
        $this->adresse_depart = $adresse_depart;

        return $this;
    }

    public function getCodePostalDepart(): ?int
    {
        return $this->code_postal_depart;
    }

    public function setCodePostalDepart(int $code_postal_depart): static
    {
        $this->code_postal_depart = $code_postal_depart;

        return $this;
    }

    public function getVilleDepart(): ?string
    {
        return $this->ville_depart;
    }

    public function setVilleDepart(string $ville_depart): static
    {
        $this->ville_depart = $ville_depart;

        return $this;
    }
    public function getAdresseArrivee(): ?string
    {
        return $this->adresse_arrivee;
    }

    public function setAdresseArrivee(string $adresse_arrivee): static
    {
        $this->adresse_arrivee = $adresse_arrivee;

        return $this;
    }

    public function getCodePostalArrivee(): ?int
    {
        return $this->code_postal_arrivee;
    }

    public function setCodePostalArrivee(int $code_postal_arrivee): static
    {
        $this->code_postal_arrivee = $code_postal_arrivee;

        return $this;
    }

    public function getVilleArrivee(): ?string
    {
        return $this->ville_arrivee;
    }

    public function setVilleArrivee(string $ville_arrivee): static
    {
        $this->ville_arrivee = $ville_arrivee;

        return $this;
    }

    

    public function getTrip(): ?Trip
    {
        return $this->trip;
    }

    public function setTrip(?Trip $trip): static
    {
        $this->trip = $trip;

        return $this;

        
    }

    
}
