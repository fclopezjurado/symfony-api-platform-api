<?php

namespace App\Service\User;

use App\Entity\User;
use App\Exception\User\UserAlreadyExistsException;
use App\Messenger\Message\UserRegisteredMessage;
use App\Messenger\RoutingKey;
use App\Repository\UserRepository;
use App\Service\Password\EncoderService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;

class UserRegisterService
{
    private UserRepository $userRepository;
    private EncoderService $encoderService;
    private MessageBusInterface $messageBus;

    /**
     * UserRegisterService constructor.
     * @param UserRepository $userRepository
     * @param EncoderService $encoderService
     * @param MessageBusInterface $messageBus
     */
    public function __construct(
        UserRepository $userRepository,
        EncoderService $encoderService,
        MessageBusInterface $messageBus
    ) {
        $this->userRepository = $userRepository;
        $this->encoderService = $encoderService;
        $this->messageBus = $messageBus;
    }

    public function create(string $name, string $email, string $password): User
    {
        $user = new User($name, $email);

        $user->setPassword($this->encoderService->generateEncodedPassword($user, $password));

        try {
            $this->userRepository->save($user);
        } catch (ORMException|OptimisticLockException $exception) {
            UserAlreadyExistsException::fromEmail($email);
        }

        $this->messageBus->dispatch(
            new UserRegisteredMessage(
                $user->getId(),
                $user->getName(),
                $user->getEmail(),
                $user->getToken()
            ), [new AmqpStamp(RoutingKey::USER_QUEUE)]
        );

        return $user;
    }
}
