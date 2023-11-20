<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Reservation;
use App\Entity\User;
use App\Entity\Trajet;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use App\Repository\TrajetRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


class ReservationController extends AbstractController
{
    private $manager;
    private $user;

    public function __construct(EntitymanagerInterface $manager, UserRepository $user, TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager)
    {
        $this->manager = $manager;
        $this->user = $user;
        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
    }

    #[Route ('/api/reservation', name: 'app_reservation', methods: ['POST'])]
    public function ajouterReservation(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): Response
    {
        $token = $tokenStorage->getToken();
        $user = $token->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(['message' => 'Utilisateur non authentifie.'], 401);
        }

        $data = json_decode($request->getContent(), true);

        $statut = $data['statut'];
        $trajet_id = $data['trajet_id'];
        $date_reservation = new \DateTime();

        $reservation = new Reservation();
        $reservation->setUser($user);
        $reservation->setTrajet($this->manager->getRepository(Trajet::class)->find($trajet_id));
        $reservation->setStatut($statut);
        $reservation->setDateReservation($date_reservation);

        $entityManager->persist($reservation);

        $entityManager->flush();    

        return new JsonResponse(['message' => 'Réservation ajoutée avec succès.'], 200);
    }
}
