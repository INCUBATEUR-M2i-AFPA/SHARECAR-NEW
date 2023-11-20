<?php

namespace App\Dto;

final class CarDto
{
    private ?int $id;
    private ?int $nbre_de_places;
    private ?int $nbre_petits_bagages;
    private ?int $nbre_grands_bagages;
    private ?UserDto $user;
    private ?ModelesDto $modeles;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getNbreDePlaces(): ?int
    {
        return $this->nbre_de_places;
    }

    public function setNbreDePlaces(?int $nbre_de_places): void
    {
        $this->nbre_de_places = $nbre_de_places;
    }

    public function getNbrePetitsBagages(): ?int
    {
        return $this->nbre_petits_bagages;
    }

    public function setNbrePetitsBagages(?int $nbre_petits_bagages): void
    {
        $this->nbre_petits_bagages = $nbre_petits_bagages;
    }

    public function getNbreGrandsBagages(): ?int
    {
        return $this->nbre_grands_bagages;
    }

    public function setNbreGrandsBagages(?int $nbre_grands_bagages): void
    {
        $this->nbre_grands_bagages = $nbre_grands_bagages;
    }

    public function getUser(): ?UserDto
    {
        return $this->user;
    }

    public function setUser(?UserDto $user): void
    {
        $this->user = $user;
    }

    public function getModeles(): ?ModelesDto
    {
        return $this->modeles;
    }

    public function setModeles(?ModelesDto $modeles): void
    {
        $this->modeles = $modeles;
    }
}
