<?php

namespace App\Controller;

use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
// use Symfony\Component\Security\Core\Security;

class ConnexionController extends AbstractController

{
    
    #[Route('/connexion', name: 'app_connexion', methods: ['POST'])]


    public function connexion(Request $request, UtilisateurRepository $utilisateurRepository) : Response
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'];
        $mot_de_passe = $data['mot_de_passe'];

        $utilisateur = $utilisateurRepository->findOneByEmail($email);

        if (!$utilisateur || !password_verify($mot_de_passe, $utilisateur->getMotDePasse())) {
            return $this->json(['message' => 'Email ou mot de passe invalide'], 403);
        }

        $id = [
            $utilisateur->getId(),
        ];

        return $this->json($id);
    }
}