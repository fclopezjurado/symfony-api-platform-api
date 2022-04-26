<?php

namespace App\Service\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Password\EncoderService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class ResetPasswordService
{
    private UserRepository $userRepository;

    private EncoderService $encoderService;

    /**
     * ResetPasswordService constructor.
     */
    public function __construct(UserRepository $userRepository, EncoderService $encoderService)
    {
        $this->userRepository = $userRepository;
        $this->encoderService = $encoderService;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function reset(string $userId, string $resetPasswordToken, string $password): User
    {
        $user = $this->userRepository->findOneByIdAndResetPasswordToken($userId, $resetPasswordToken);

        $user->setPassword($this->encoderService->generateEncodedPassword($user, $password));
        $user->setResetPasswordToken(null);
        $this->userRepository->save($user);

        return $user;
    }
}
