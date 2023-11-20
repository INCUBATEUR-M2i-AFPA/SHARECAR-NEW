<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Modeles;
use App\Entity\Car; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Repository\ModelesRepository;
use Symfony\Component\HttpFoundation\Request;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Repository\CarRepository;

class CarController extends AbstractController
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

    #[Route('/api_modeles', name: 'car', methods: ['GET'])]
    public function voitureModeles(Request $request, JWTTokenManagerInterface $jwtManager, TokenStorageInterface $tokenStorage, UserRepository $user, ModelesRepository $modeles): Response
    {
        $modeles = $modeles->findAll();

        return $this->json($modeles);
    }

    #[Route('/api/ajouter_voiture', name: 'app_ajouter_voiture', methods: ['POST'])]
    public function ajouterVoiture(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérez l'utilisateur connecté
        $user = $this->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['message' => 'Utilisateur non authentifié.'], 401);
        }

        // Récupérez les données de la requête
        $data = json_decode($request->getContent(), true);

        // Vérifiez si toutes les données nécessaires sont présentes
        if (!isset($data['modele_id']) || !isset($data['nbre_de_places']) || !isset($data['nbre_petits_bagages']) || !isset($data['nbre_grands_bagages'])) {
            return new JsonResponse(['message' => 'Les champs requis sont manquants.'], 400);
        }

        // Récupérez le modèle de la base de données
        $modeleId = $data['modele_id'];
        $modele = $entityManager->getRepository(Modeles::class)->find($modeleId);

        if (!$modele) {
            return new JsonResponse(['message' => 'Modèle non trouvé.'], 404);
        }

        // Créez une nouvelle voiture
        $car = new Car(); 
        $car->setModeles($modele);
        $car->setUser($user);
        $car->setNbreDePlaces($data['nbre_de_places']);
        $car->setNbrePetitsBagages($data['nbre_petits_bagages']);
        $car->setNbreGrandsBagages($data['nbre_grands_bagages']);

        // Persistez la voiture en base de données
        $entityManager->persist($car);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Voiture ajoutée avec succès.']);
    }

    #[Route('/api/voitures', name: 'app_voitures', methods: ['GET'])]
    public function getVoitures(): JsonResponse
    {
        // Récupérez l'utilisateur connecté
        $user = $this->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['message' => 'Utilisateur non authentifié.'], 401);
        }

        // Récupérez les voitures de l'utilisateur
        $cars = $this->getDoctrine()->getRepository(\App\Entity\Car::class)->findBy(['user' => $user]);

        // Transformez les données des voitures en un format adapté pour la réponse JSON
        $formattedCars = [];
        foreach ($cars as $car) {
            $formattedCars[] = [
                'modele' => $car->getModeles()->getNom(), // Vous pouvez ajuster les champs en fonction de votre modèle de données
                'nbre_de_places' => $car->getNbreDePlaces(),
                'nbre_petits_bagages' => $car->getNbrePetitsBagages(),
                'nbre_grands_bagages' => $car->getNbreGrandsBagages(),
            ];
        }

        return new JsonResponse($formattedCars);
    }

    #[Route('/api/mycars', name: 'app_voiture', methods: ['GET'])]
    public function getMesVoitures(CarRepository $carRepository): JsonResponse
    {
        $user = $this->getUser();
    
        if (!$user instanceof User) {
            return new JsonResponse(['message' => 'Utilisateur non authentifié.'], 401);
        }
    
        $cars = $carRepository->findBy(['user' => $user], ['modeles' => 'ASC']);
    
        $formattedCars = [];
        foreach ($cars as $car) {
            $modele = $car->getModeles();
            $modeleNom = $modele ? $modele->getModele() : null;
    
            $formattedCars[] = [
                'car' => [
                    'modele' => $modeleNom,
                    'nbre_de_places' => $car->getNbreDePlaces(),
                    'nbre_petits_bagages' => $car->getNbrePetitsBagages(),
                    'nbre_grands_bagages' => $car->getNbreGrandsBagages(),
                ],
                'modele_details' => $modele ? [
                    'marque' => $modele->getMarque(),
                    'modele' => $modele->getModele(),
                ] : null,
            ];
        }
    
        return $this->json($formattedCars);
    }

}
