<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\TripServiceInterface;
use App\Dto\TripDto;

class TripController extends AbstractController
{
    private $tripService;

    public function __construct(TripServiceInterface $tripService)
    {
        $this->tripService = $tripService;
    }

    #[Route('/api/add_trip', name: 'app_add_trip', methods: ['POST'])]
    public function addTrip(Request $request): JsonResponse
    {
        return $this->tripService->addTrip($request);
    }

    #[Route('/api/get_all_trips', name: 'app_get_all_trips', methods: ['GET'])]
    public function getAllTrips(): JsonResponse
    {
        return $this->tripService->getAllTrips();
    }

    #[Route('/api/supprimer_trajet', name: 'app_supprimer_trajet', methods: ['POST'])]
    public function supprimerTrajet(Request $request): JsonResponse
    {
        return $this->tripService->supprimerTrajet($request);
    }
}