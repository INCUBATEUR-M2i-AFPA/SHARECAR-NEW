<?php

namespace App\Controller;

use App\Entity\ImageVoitures;
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

class ImagesVoitureController extends AbstractController
{
    private $manager;
    private $user;

    public function __construct(EntityManagerInterface $manager, UserRepository $user, TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager)
    {
         $this->manager = $manager;
         $this->user = $user;
         $this->jwtManager = $jwtManager;
         $this->tokenStorageInterface = $tokenStorageInterface;
    }

    #[Route('/api/images_voiture', name: 'app_images_voiture', methods: ['GET'])]
    public function imagesVoiture(Request $request, JWTTokenManagerInterface $jwtManager, TokenStorageInterface $tokenStorage, UserRepository $user): Response
    {
        $token = $tokenStorage->getToken();
        $user = $token->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['message' => 'User non authentifié.'], 401);
        }

        return $this->json($user);
    }

    #[Route('/api/useraddurlvoitures', name: 'image_voiture_url', methods: ['POST'])]
    public function addImagevoiture(Request $request, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): JsonResponse
    {
        $token = $tokenStorage->getToken();
        $user = $token->getUser();
    
        if (!$user instanceof User) {
            return new JsonResponse(['message' => 'User non authentifié.'], 401);
        }
    
        $data = json_decode($request->getContent(), true);
    
        if (isset($data['image_voiture_urls']) && is_array($data['image_voiture_urls'])) {
            $imageUrls = $data['image_voiture_urls'];
    
            foreach ($imageUrls as $imageUrl) {
                $image = new ImageVoitures();
                $image->setImageUrl($imageUrl);
                $image->setUser($user);
    
                $entityManager->persist($image);
            }
    
            $entityManager->flush();
    
            return new JsonResponse(['message' => 'Images ajoutées avec succès.']);
        } else {
            return new JsonResponse(['message' => 'Les URLs des images sont manquantes ou au mauvais format.'], 400);
        }
    }
    
    
    #[Route('/api/get_voitures_images', name: 'voitures_images', methods: ['GET'])]
    public function getUserImages(TokenStorageInterface $tokenStorage): JsonResponse
    {
        $token = $tokenStorage->getToken();
        $user = $token->getUser();
    
        if (!$user instanceof User) {
            return new JsonResponse(['message' => 'User non authentifié.'], 401);
        }
    
        $images = $user->getImageVoitures(); // Utilisez getImageVoitures au lieu de getImages
        $imageData = [];
    
        foreach ($images as $image) {
            $imageData[] = [
                'id' => $image->getId(),
                'url' => $image->getImageUrl(), // Utilisez getImageUrl au lieu de getUrl
            ];
        }
    
        return new JsonResponse(['imagesVoiture' => $imageData]);
    }

    #[Route('/api/delete_voiture_image/{id}', name: 'delete_voiture_image', methods: ['DELETE'])]
    public function deleteVoitureImage(Request $request, $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $image = $entityManager->getRepository(ImageVoitures::class)->find($id);
        if (!$image) {
            return new JsonResponse(['message' => 'Image non trouvée.'], 404);
        }
    
  
        $entityManager->remove($image);
        $entityManager->flush();
    
        return new JsonResponse(['message' => 'Image supprimée avec succès.']);
    }
    
}
