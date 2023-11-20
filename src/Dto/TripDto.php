<?php

namespace App\Dto;

class TripDto
{
    public  ?int $id;
    public  ?int $prix;
    public  ?bool $fumeur;
    public  ?bool $silence;
    public  ?bool $musique;
    public  ?bool $animaux;
    public  ?string $date_depart;
    public  ?string $heure_depart;
    public  ?int $carId;
    public  ?int $userId;

    public array $etapes;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(?int $prix): void
    {
        $this->prix = $prix;
    }

    public function isFumeur(): ?bool
    {
        return $this->fumeur;
    }

    public function setFumeur(?bool $fumeur): void
    {
        $this->fumeur = $fumeur;
    }

    public function isSilence(): ?bool
    {
        return $this->silence;
    }

    public function setSilence(?bool $silence): void
    {
        $this->silence = $silence;
    }

    public function isMusique(): ?bool
    {
        return $this->musique;
    }

    public function setMusique(?bool $musique): void
    {
        $this->musique = $musique;
    }

    public function isAnimaux(): ?bool
    {
        return $this->animaux;
    }

    public function setAnimaux(?bool $animaux): void
    {
        $this->animaux = $animaux;
    }

    public function getDateDepart(): ?string
    {
        return $this->date_depart;
    }

    public function setDateDepart(?string $date_depart): void
    {
        $this->date_depart = $date_depart;
    }

    public function getHeureDepart(): ?string
    {
        return $this->heure_depart;
    }

    public function setHeureDepart(?string $heure_depart): void
    {
        $this->heure_depart = $heure_depart;
    }

    public function getCarId(): ?int
    {
        return $this->carId;
    }

    public function setCarId(?int $carId): void
    {
        $this->carId = $carId;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    public function getEtapes(): array
    {
        return $this->etapes;
    }

    public function setEtapes(array $etapes): void
    {
        $this->etapes = $etapes;
    }
}