<?php

namespace App\Entity;

use App\Repository\DialRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
 
#[ApiResource]

#[ORM\Entity(repositoryClass: DialRepository::class)]
class Dial
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column(length: 255)]
    private ?string $sujet = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datetime_dial = null;

    #[ORM\ManyToOne]
     #[ORM\JoinColumn(nullable: false)]
     private ?User $user = null;
    
     #[ORM\ManyToOne]
     #[ORM\JoinColumn(nullable: false)]
     private ?User $user1 = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): static
    {
        $this->sujet = $sujet;

        return $this;
    }

    public function getDatetimeDial(): ?\DateTimeInterface
    {
        return $this->datetime_dial;
    }

    public function setDatetimeDial(\DateTimeInterface $datetime_dial): static
    {
        $this->datetime_dial = $datetime_dial;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getUser1(): ?User
    {
        return $this->user1;
    }

    public function setUser1(?User $user1): static
    {
        $this->user1 = $user1;

        return $this;
    }
}
