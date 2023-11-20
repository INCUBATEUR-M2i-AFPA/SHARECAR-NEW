<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Entity(mapped=false)
 */
class UserDto
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $nom = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $prenom = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 255)]
    private ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $mot_de_passe = null;

    #[Assert\NotBlank]
    #[Assert\DateTime]
    private ?\DateTimeInterface $date_inscription = null;

    #[Assert\Length(max: 255)]
    private ?string $pseudo = null;

    #[Assert\NotNull]
    private ?int $credit_jeton = null;

    #[Assert\NotNull]
    private array $roles = [];

    #[Assert\Length(max: 255)]
    private ?string $adresse = null;

    #[Assert\Length(max: 255)]
    private ?string $code_postal = null;

    #[Assert\Length(max: 255)]
    private ?string $ville = null;

    #[Assert\Date]
    private ?string $date_de_naissance = null;

    #[Assert\Length(max: 255)]
    private ?string $confirmationToken = null;

    #[Assert\Length(max: 255)]
    private ?string $resetPasswordToken = null;

    #[Assert\NotNull]
    private bool $enabled = false;

    #[Assert\Length(max: 255)]
    private ?string $biographie = null;


    public function toArray(): array
    {
        return [
            'nom' => $this->getNom(),
            'prenom' => $this->getPrenom(),
            'email' => $this->getEmail(),
            'mot_de_passe' => $this->getMotDePasse(),
            'date_inscription' => $this->getDateInscription(),
            'pseudo' => $this->getPseudo(),
            'credit_jeton' => $this->getCreditJeton(),
            'roles' => $this->getRoles(),
            'adresse' => $this->getAdresse(),
            'code_postal' => $this->getCodePostal(),
            'ville' => $this->getVille(),
            'date_de_naissance' => $this->getDateDeNaissance(),
            'confirmation_token' => $this->getConfirmationToken(),
            'reset_password_token' => $this->getResetPasswordToken(),
            'enabled' => $this->getEnabled(),
            'biographie' => $this->getBiographie(),
        ];
    }


    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->mot_de_passe;
    }

    public function setMotDePasse(?string $mot_de_passe): self
    {
        $this->mot_de_passe = $mot_de_passe;

        return $this;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->date_inscription;
    }

    public function setDateInscription(?\DateTimeInterface $date_inscription): self
    {
        $this->date_inscription = $date_inscription;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getCreditJeton(): ?int
    {
        return $this->credit_jeton;
    }

    public function setCreditJeton(?int $credit_jeton): self
    {
        $this->credit_jeton = $credit_jeton;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(?string $code_postal): self
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getDateDeNaissance(): ?string
    {
        return $this->date_de_naissance;
    }

    public function setDateDeNaissance(?string $date_de_naissance): self
    {
        $this->date_de_naissance = $date_de_naissance;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getResetPasswordToken(): ?string
    {
        return $this->resetPasswordToken;
    }

    public function setResetPasswordToken(?string $resetPasswordToken): self
    {
        $this->resetPasswordToken = $resetPasswordToken;

        return $this;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getBiographie(): ?string
    {
        return $this->biographie;
    }

    public function setBiographie(?string $biographie): self
    {
        $this->biographie = $biographie;

        return $this;
    }
}
