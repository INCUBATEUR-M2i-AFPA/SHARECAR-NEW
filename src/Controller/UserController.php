<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class UserController extends AbstractController

{
    private $manager;
    private $user;

    public function __construct(EntityManagerInterface $manager, UserRepository $user)
    {
         $this->manager=$manager;

         $this->user=$user;
    }

    #[Route('/api/users_liste', name: 'users_liste', methods: ['GET'])]
    public function user(): Response
    {
        $users=$this->manager->getRepository(User::class)->findAll();

        return $this->json($users);
    }
}