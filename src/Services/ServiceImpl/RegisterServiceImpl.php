<?php

namespace App\Services\ServiceImpl;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\Validator\ValidationService; 
use App\Services\RegisterServiceInterface;
use App\Dto\UserDto;
use App\Mapper\UserMapper;

/**
 * @Service
 */
class RegisterServiceImpl implements RegisterServiceInterface
{
    private $manager;
    private $userRepository;
    private $mailer;
    private $twig;
    private $passwordHasher;
    private $urlGenerator;
    private $validationService;
    private $userMapper;

    public function __construct(
        EntityManagerInterface $manager,
        UserRepository $userRepository,
        MailerInterface $mailer,
        Environment $twig,
        UserPasswordHasherInterface $passwordHasher,
        UrlGeneratorInterface $urlGenerator,
        ValidationService $validationService,
        UserMapper $userMapper
    ) {
    
        $this->manager = $manager;
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->passwordHasher = $passwordHasher;
        $this->urlGenerator = $urlGenerator; 
        $this->validationService = $validationService;
        $this->userMapper = $userMapper;
    }

    public function register(array $data): array
    {
         // Utilisez le mapper pour convertir les données du tableau en objet UserDto
         $userDto = $this->userMapper->convertArrayToUserDto($data);

         // Validez les données à l'aide du service de validation
         $validationResult = $this->validationService->validateDataRegistration($userDto->toArray());

        if (!$validationResult['status']) {
            return $validationResult;
        }

        $emailExist = $this->userRepository->findOneByEmail($data['email']);

        if ($emailExist) {
            return ['status' => false, 'message' => 'Cet email existe déjà, veuillez le changer.'];
        }

        $hashedPassword = $this->passwordHasher->hashPassword(new User(), $data['mot_de_passe']);

        $user = new User();
        $user = $this->userMapper->convertUserDtoToEntity($userDto, $user);
        $user->setNom($data['nom']);
        $user->setPrenom($data['prenom']);
        $user->setPseudo($data['pseudo']);
        $user->setDateInscription(new \DateTime());
        $user->setCreditJeton(50);
        $user->setEmail($data['email']);
        $user->setMotDePasse($hashedPassword);
        $user->setRoles(['ROLE_USER']);

        $confirmationToken = bin2hex(random_bytes(32));
        $user->setConfirmationToken($confirmationToken);
        $user->setEnabled(false);

        $this->manager->persist($user);
        $this->manager->flush();

        $this->sendEmailConfirmationRegister($user->getEmail(), $confirmationToken);

        return ['status' => true, 'message' => 'Inscription effectuée avec succès. Veuillez vérifier votre email pour activer votre compte.'];
    }

    public function confirmRegister(string $token): array
    {
        $user = $this->userRepository->findOneBy(['confirmationToken' => $token]);

        if (!$user) {
            return ['status' => false, 'message' => 'Lien de confirmation non valide.'];
        }

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $this->manager->persist($user);
        $this->manager->flush();

        return ['status' => true, 'message' => 'Email confirmé. Vous pouvez maintenant vous connecter.'];
    }

    public function forgotPassword(string $email): array
{
    $user = $this->userRepository->findOneBy(['email' => $email]);

    if (!$user) {
        return ['status' => false, 'message' => 'Aucun user trouvé avec cette adresse email.'];
    }

    // Générez un jeton de réinitialisation de mot de passe
    $token = bin2hex(random_bytes(32));

    // Enregistrez le jeton de réinitialisation de mot de passe dans la base de données (dans la table user)
    $user->setResetPasswordToken($token);
    $this->manager->flush();

    // Envoi de l'e-mail de réinitialisation
    $this->sendEmailForgotPassword($user->getEmail(), $token, $user->getNom(), $user->getPrenom());

    return [
        'status' => true,
        'message' => 'Un e-mail de réinitialisation de mot de passe a été envoyé avec succès.',
        'resetPasswordToken' => $token,
    ];
}


    public function resetPassword(string $token, string $password): array
{
    $user = $this->userRepository->findOneBy(['resetPasswordToken' => $token]);

    if (!$user) {
        return ['status' => false, 'message' => 'Jeton de réinitialisation de mot de passe expiré.'];
    }

    $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
    $user->setMotDePasse($hashedPassword);
    $user->setResetPasswordToken(null);

    $this->manager->flush();

    return ['status' => true, 'message' => 'Mot de passe réinitialisé avec succès.'];
}


    private function sendEmailConfirmationRegister(string $email, string $token): void
{
    $confirmationUrl = $this->urlGenerator->generate('app_confirm_register', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

    $email = (new Email())
        ->from(new Address('noreply@example.com', 'Mon application'))
        ->to($email)
        ->subject('Confirmez votre inscription')
        ->html($this->twig->render('emails/confirmation_email.html.twig', ['confirmationUrl' => $confirmationUrl]));

    $this->mailer->send($email);
}


    private function sendEmailForgotPassword(string $email, string $token, string $firstName, string $lastName): void
    {
        $resetPasswordUrl = $this->urlGenerator->generate('reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
    
        $email = (new Email())
            ->from(new Address('noreply@example.com', 'Mon application'))
            ->to($email)
            ->subject('Réinitialisez votre mot de passe')
            ->html(
                $this->twig->render('resetPasswordEmail/resetpassword.html.twig', [
                    'resetPasswordUrl' => $resetPasswordUrl,
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'token' => $token,
                ])
            );
    
        $this->mailer->send($email);
    }
}