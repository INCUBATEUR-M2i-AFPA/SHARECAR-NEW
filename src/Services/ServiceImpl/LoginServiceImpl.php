<?php

namespace App\Services\ServiceImpl;

use App\Dto\UserDto;
use App\Services\LoginServiceInterface;
use App\Mapper\UserMapper;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Repository\UserRepository;

class LoginServiceImpl implements LoginServiceInterface
{
    private JWTTokenManagerInterface $jwtManager;
    private PropertyInfoExtractorInterface $propertyInfoExtractor;
    private $propertyAccessor;
    private UserRepository $userRepository;
    private UserMapper $userMapper;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        UserRepository $userRepository,
        UserMapper $userMapper
    ) {
        $this->jwtManager = $jwtManager;
        $this->userRepository = $userRepository;
        $this->userMapper = $userMapper;

        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();

        $reflectionExtractor = new ReflectionExtractor();
        $phpDocExtractor = new PhpDocExtractor();

        $this->propertyInfoExtractor = new PropertyInfoExtractor(
            [$reflectionExtractor],
            [$phpDocExtractor, $reflectionExtractor]
        );
    }

    public function loginUser(array $data): array
    {
        $email = $data['email'];
        $mot_de_passe = $data['mot_de_passe'];

        $user = $this->userRepository->findOneByEmail($email);

        if ($user === null || !password_verify($mot_de_passe, $user->getPassword())) {
            return ['message' => 'Email ou mot de passe invalide', 'status' => 403];
        }

        $payload = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ];

        $token = $this->jwtManager->create($user, $payload);

        return ['token' => $token];
    }
}