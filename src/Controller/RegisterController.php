<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Services\RegisterServiceInterface;

class RegisterController extends AbstractController
{
    private $registerService;

    public function __construct(RegisterServiceInterface $registerService)
    {
        $this->registerService = $registerService;
    }

    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Appelez la fonction du service d'inscription pour gérer la logique d'inscription
        $response = $this->registerService->register($data);

        return new JsonResponse($response, $response['status'] ? JsonResponse::HTTP_OK : JsonResponse::HTTP_BAD_REQUEST);
    }

    #[Route('/confirm-register/{token}', name: 'app_confirm_register', methods: ['GET'])]
    public function confirmRegister(string $token): JsonResponse
    {
        // Appelez la fonction du service pour confirmer l'inscription
        $response = $this->registerService->confirmRegister($token);

        return new JsonResponse($response, $response['status'] ? JsonResponse::HTTP_OK : JsonResponse::HTTP_BAD_REQUEST);
    }

    #[Route('/forgotPassword', name: 'forgot-password', methods: ['POST'])]
    public function forgotPassword(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérifiez si la clé "email" existe dans le tableau $data
        if (!isset($data['email'])) {
            return new JsonResponse(['status' => false, 'message' => 'La clé "email" est manquante dans les données.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Appelez la fonction du service pour demander la réinitialisation du mot de passe
        $response = $this->registerService->forgotPassword($data['email']);

        return new JsonResponse($response, $response['status'] ? JsonResponse::HTTP_OK : JsonResponse::HTTP_BAD_REQUEST);
    }

    #[Route('/resetpassword/{token}', name: 'reset_password', methods: ['POST'])]
    public function resetPassword(Request $request, string $token, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Appelez la fonction du service pour réinitialiser le mot de passe
        $response = $this->registerService->resetPassword($token, $data['newpassword'], $passwordHasher);

        return new JsonResponse($response, $response['status'] ? JsonResponse::HTTP_OK : JsonResponse::HTTP_BAD_REQUEST);
    }
}
