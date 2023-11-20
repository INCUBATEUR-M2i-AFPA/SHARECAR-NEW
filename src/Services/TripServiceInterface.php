<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

interface TripServiceInterface
{
    public function addTrip(Request $request): JsonResponse;

    public function getAllTrips(): JsonResponse;

    public function supprimerTrajet(Request $request): JsonResponse;
}
