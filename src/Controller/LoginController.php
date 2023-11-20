<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\LoginServiceInterface;
use App\Dto\UserDto;

class LoginController extends AbstractController
{
    private LoginServiceInterface $loginService;

    public function __construct(LoginServiceInterface $loginService)
    {
        $this->loginService = $loginService;
    }

    #[Route('/login', name: 'app_connexion', methods: ['POST'])]
    public function connlogloginixion(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $response = $this->loginService->loginUser($data);

        return $this->json($response);
    }
}
