<?php

namespace App\Controller;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;

class ProfilController extends AbstractController
{
    private $manager;
    private $user;

    public function __construct(EntityManagerInterface $manager, UserRepository $user, TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager)
    {
         $this->manager=$manager;
         $this->user=$user;
         $this->jwtManager = $jwtManager;
         $this->tokenStorageInterface = $tokenStorageInterface;
    }

    #[Route('/api/profil', name: 'app_profil', methods: ['GET'])]
    public function profil(Request $request, JWTTokenManagerInterface $jwtManager, TokenStorageInterface $tokenStorage, UserRepository $user): Response
    {
        $token = $tokenStorage->getToken();
        $user = $token->getUser();
    
        if (!$user instanceof User) {
            return new JsonResponse(['message' => 'User non authentifié.'], 401);
        }
    
        return $this->json($user);
    }
    

    #[Route('/api/profil_modif', name: 'app_profil_modif', methods: ['PUT'])]
    public function profilModif(Request $request, UserRepository $userRepository, JWTTokenManagerInterface $jwtManager, EntityManagerInterface $manager): Response
    { 
      $data=json_decode($request->getContent(),true);
      
      $nom=$data['nom'];
      $prenom=$data['prenom'];
      $pseudo=$data['pseudo'];
      $email=$data['email'];
      $adresse=$data['adresse'];
      $code_postal=$data['code_postal'];
      $ville=$data['ville'];
      $date_de_naissance=$data['date_de_naissance'];
;
      $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());
      $email = $decodedJwtToken['username'];
      $user = $userRepository->findOneByEmail($email);

      if(!$user)
      {
        return new JsonResponse
        (
            [
              'status'=>false,
              'message'=>'User non trouve'

            ]

        );
      }
      if($user)
       {         
         $user->setNom($nom);
         $user->setPrenom($prenom);
         $user->setPseudo($pseudo);
         $user->setEmail($email);
         $user->setAdresse($adresse);
         $user->setCodePostal($code_postal);
         $user->setVille($ville);
         $user->setDateDeNaissance($date_de_naissance);
       }

       $this->manager->persist($user);
       $this->manager->flush();

       return new JsonResponse
       (
           [
             'status'=>true,
             'message'=>'Modification effectuée avec succés'
           ]
           );
    }
    #[Route('/api/profil_modif_bio', name: 'app_profil_modif_bio', methods: ['PUT'])]
    public function profilModifBio(Request $request, UserRepository $userRepository, JWTTokenManagerInterface $jwtManager, EntityManagerInterface $manager): Response
    { 
      $data=json_decode($request->getContent(),true);
    
      $biographie=$data['biographie'];
;
      $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());
      $email = $decodedJwtToken['username'];
      $user = $userRepository->findOneByEmail($email);

      if(!$user)
      {
        return new JsonResponse
        (
            [
              'status'=>false,
              'message'=>'User non trouve'

            ]

        );
      }
      if($user)
       {         
         $user->setBiographie($biographie);

       }

       $this->manager->persist($user);
       $this->manager->flush();

       return new JsonResponse
       (
           [
             'status'=>true,
             'message'=>'Modification effectuée avec succés'
           ]
           );
    }


    #[Route('/api/useraddurl', name: 'user_add_url', methods: ['POST'])]
    public function addImage(Request $request, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): JsonResponse
    {
        $token = $tokenStorage->getToken();
        $user = $token->getUser();
    
        if (!$user instanceof User) {
            return new JsonResponse(['message' => 'User non authentifié.'], 401);
        }
    
        $data = json_decode($request->getContent(), true);
    
        if (isset($data['image_url'])) {
            // Créez une nouvelle entité Image, associez-la à l'user et enregistrez-la en base de données
            $imageUrl = $data['image_url'];
            $image = new Image();
            $image->setUrl($imageUrl);
            $image->setUser($user);
    
            $entityManager->persist($image);
            $entityManager->flush();
    
            return new JsonResponse(['message' => 'Image ajoutée avec succès.']);
        } else {
            return new JsonResponse(['message' => 'L\'URL de l\'image est manquante.'], 400);
        }
    }

    #[Route('/api/get_user_images', name: 'user_images', methods: ['GET'])]
    public function getUserImages(TokenStorageInterface $tokenStorage): JsonResponse
    {
        $token = $tokenStorage->getToken();
        $user = $token->getUser();
    
        if (!$user instanceof User) {
            return new JsonResponse(['message' => 'User non authentifié.'], 401);
        }
    
        $images = $user->getImages();
        $imageData = [];
    
        foreach ($images as $image) {
            $imageData[] = [
                'id' => $image->getId(), // Ajoutez l'ID de l'image
                'url' => $image->getUrl(),
            ];
        }
    
        return new JsonResponse(['images' => $imageData]);
    }

    #[Route('/api/profil/updatepassword', name: 'app_modification_mot_de_passe', methods: ['POST'])]
    public function modifierMotDePasse(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, JWTTokenManagerInterface $jwtManager, TokenStorageInterface $tokenStorage): Response
    {
        // Récupérez le token JWT de l'user actuel
        $token = $tokenStorage->getToken();
        
        if (!$token) {
            return new JsonResponse(['message' => 'User non authentifié.'], 401);
        }
    
        // Vérifiez si le token est valide en essayant de le décoder
        try {
            $user = $jwtManager->decode($token);
        } catch (JWTDecodeFailureException $e) {
            return new JsonResponse(['message' => 'Jeton invalide.'], 401);
        }
 
    $user = $this->getUser();

    if (!$user instanceof User) {
        return new JsonResponse(['message' => 'User non authentifié.'], 401);
    }

    $data = json_decode($request->getContent(), true);

    if (!isset($data['ancien_mot_de_passe']) || !isset($data['nouveau_mot_de_passe'])) {
        return new JsonResponse(['message' => 'Les champs requis sont manquants.'], 400);
    }


    $ancienMotDePasse = $data['ancien_mot_de_passe'];
    if (!$passwordHasher->isPasswordValid($user, $ancienMotDePasse)) {
        return new JsonResponse(['message' => 'Mot de passe actuel incorrect.'], 400);
    }

    $nouveauMotDePasse = $data['nouveau_mot_de_passe'];

    $hashedPassword = $passwordHasher->hashPassword($user, $nouveauMotDePasse);
    $user->setMotDePasse($hashedPassword);

    $entityManager->flush();

    return new JsonResponse(['message' => 'Mot de passe modifié avec succès.'], 200);
}

#[Route('/api/desactiver_profil', name: 'app_profil_desactiver', methods: ['POST'])]
public function desactiverCompte(Request $request, EntityManagerInterface $entityManager): JsonResponse
{
    $user = $this->getUser();

    if (!$user instanceof User) {
        return new JsonResponse(['message' => 'User non authentifié.'], 401);
    }

    // Ajoutez le code pour désactiver le compte de l'user ici.
    $user->setEnabled(false);

    $entityManager->persist($user);
    $entityManager->flush();

    return new JsonResponse(['message' => 'Compte désactivé avec succès.']);
}




}
