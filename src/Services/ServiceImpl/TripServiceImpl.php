<?php

namespace App\Services\ServiceImpl;

use App\Entity\User;
use App\Entity\Car;
use App\Entity\Trip;
use App\Entity\Etape;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Services\TripServiceInterface;

/**
 * @Service
 */
class TripServiceImpl implements TripServiceInterface
{
    private $entityManager;
    private $userRepository;
    private $tokenStorage;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        TokenStorageInterface $tokenStorage
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function addTrip(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['message' => 'Utilisateur non authentifié.'], 401);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['voiture_id']) || !isset($data['prix']) || !isset($data['fumeur']) || !isset($data['silence']) || !isset($data['musique']) || !isset($data['animaux']) || !isset($data['date_depart']) || !isset($data['heure_depart'])) {
            return new JsonResponse(['message' => 'Les champs requis sont manquants.'], 400);
        }

        $carId = $data['voiture_id'];
        $car = $this->entityManager->getRepository(Car::class)->find($carId);

        if (!$car) {
            return new JsonResponse(['message' => 'Voiture non trouvée.'], 404);
        }

        $trip = new Trip();
        $trip->setCar($car);
        $trip->setUser($user);
        $trip->setPrix($data['prix']);
        $trip->setFumeur($data['fumeur']);
        $trip->setSilence($data['silence']);
        $trip->setMusique($data['musique']);
        $trip->setAnimaux($data['animaux']);
        $trip->setDateDepart($data['date_depart']);
        $trip->setHeureDepart($data['heure_depart']);

        $this->entityManager->persist($trip);
        $this->entityManager->flush();

        $adresseDepart = $data['adresse_depart'];
        $codePostalDepart = $data['code_postal_depart'];
        $villeDepart = $data['ville_depart'];
        $adresseArrivee = $data['adresse_arrivee'];
        $codePostalArrivee = $data['code_postal_arrivee'];
        $villeArrivee = $data['ville_arrivee'];

        $etape = new Etape();
        $etape->setTrip($trip);
        $etape->setAdresseDepart($adresseDepart);
        $etape->setCodePostalDepart($codePostalDepart);
        $etape->setVilleDepart($villeDepart);
        $etape->setAdresseArrivee($adresseArrivee);
        $etape->setCodePostalArrivee($codePostalArrivee);
        $etape->setVilleArrivee($villeArrivee);

        $this->entityManager->persist($etape);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Etape ajoutée avec succès.']);
    }

    public function getAllTrips(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['message' => 'Utilisateur non authentifié.'], 401);
        }

        $trips = $this->entityManager->getRepository(Trip::class)->findBy(['user' => $user]);

        $formattedTrips = [];

        foreach ($trips as $trip) {
            $formattedTrips[] = $this->formatTrip($trip);
        }

        return new JsonResponse($formattedTrips);
    }

    public function supprimerTrajet(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['message' => 'Utilisateur non authentifié.'], 401);
        }

        $data = json_decode($request->getContent(), true);

        $tripId = $data['trajet_id'];

        $trip = $this->entityManager->getRepository(Trip::class)->find($tripId);

        if (!$trip) {
            return new JsonResponse(['message' => 'Trajet non trouvé.'], 404);
        }

        $etape = $this->entityManager->getRepository(Etape::class)->findOneBy(['trip' => $trip]);

        if (!$etape) {
            $this->entityManager->remove($trip);
            $this->entityManager->flush();
        } else {
            $this->entityManager->remove($etape);
            $this->entityManager->remove($trip);
            $this->entityManager->flush();
        }

        return new JsonResponse(['message' => 'Trajet supprimé avec succès.']);
    }

    private function getUser(): ?User
    {
        $token = $this->tokenStorage->getToken();
        return $token->getUser();
    }

    private function formatTrip(Trip $trip): array
    {
        $formattedTrip = [
            'id' => $trip->getId(),
            'prix' => $trip->getPrix(),
            'fumeur' => $trip->isFumeur(),
            'silence' => $trip->isSilence(),
            'musique' => $trip->isMusique(),
            'animaux' => $trip->isAnimaux(),
            'date_depart' => $trip->getDateDepart(),
            'heure_depart' => $trip->getHeureDepart(),
            'car' => [
                'id' => $trip->getCar()->getId(),
                // Ajoutez d'autres propriétés de la voiture si nécessaire
            ],
            'user' => [
                'id' => $trip->getUser()->getId(),
                // Ajoutez d'autres propriétés de l'utilisateur si nécessaire
            ],
            'etapes' => [],
        ];

        $etapes = $trip->getEtapes();

        foreach ($etapes as $etape) {
            $formattedTrip['etapes'][] = [
                'id' => $etape->getId(),
                'adresse_depart' => $etape->getAdresseDepart(),
                'code_postal_depart' => $etape->getCodePostalDepart(),
                'ville_depart' => $etape->getVilleDepart(),
                'adresse_arrivee' => $etape->getAdresseArrivee(),
                'code_postal_arrivee' => $etape->getCodePostalArrivee(),
                'ville_arrivee' => $etape->getVilleArrivee(),
            ];
        }

        return $formattedTrip;
    }
}