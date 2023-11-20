<?php

namespace App\Services\Validator;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class ValidationService
{
    public function validateDataRegistration(array $data): array
    {
        $validator = Validation::createValidator();

        $nomErrors = $validator->validate($data['nom'], [
            new Assert\NotBlank(['message' => 'Le nom ne doit pas être vide.']),
            new Assert\Length([
                'min' => 2,
                'max' => 255,
                'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.',
                'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.',
            ]),
            new Assert\Regex([
                'pattern' => '/\d/',
                'match' => false,
                'message' => 'Le nom ne doit pas contenir de chiffres.',
            ]),
        ]);
    
        if (count($nomErrors) > 0) {
            return ['status' => false, 'message' => $this->formatValidationErrors($nomErrors)];
        }
    
        $prenomErrors = $validator->validate($data['prenom'], [
            new Assert\NotBlank(['message' => 'Le prénom ne doit pas être vide.']),
            new Assert\Length([
                'min' => 2,
                'max' => 255,
                'minMessage' => 'Le prénom doit contenir au moins {{ limit }} caractères.',
                'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères.',
            ]),
            new Assert\Regex([
                'pattern' => '/\d/',
                'match' => false,
                'message' => 'Le prénom ne doit pas contenir de chiffres.',
            ]),
        ]);
    
        if (count($prenomErrors) > 0) {
            return ['status' => false, 'message' => $this->formatValidationErrors($prenomErrors)];
        }

        $emailErrors = $validator->validate($data['email'], [
            new Assert\NotBlank(['message' => "L'e-mail ne doit pas être vide."]),
            new Assert\Email(['message' => "L'e-mail n'est pas valide."]),
        ]);

        if (count($emailErrors) > 0) {
            return ['status' => false, 'message' => $this->formatValidationErrors($emailErrors)];
        }

        $passwordErrors = $this->validatePassword($data['mot_de_passe']);

        if ($passwordErrors) {
            return ['status' => false, 'message' => $passwordErrors];
        }

        return ['status' => true];
    }

    private function validatePassword(string $password): array
    {
        $validator = Validation::createValidator();
    
        $passwordErrors = $validator->validate($password, [
            new Assert\NotBlank(['message' => 'Le mot de passe ne doit pas être vide.']),
            new Assert\Length([
                'min' => 6,
                'max' => 255,
                'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                'maxMessage' => 'Le mot de passe ne peut pas dépasser {{ limit }} caractères.',
            ]),
            new Assert\Regex([
                'pattern' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
                'message' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.',
            ]),
        ]);
    
        return $this->formatValidationErrors($passwordErrors);
    }

    private function formatValidationErrors($errors): array
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = $error->getMessage();
        }

        return $errorMessages;
    }
}
