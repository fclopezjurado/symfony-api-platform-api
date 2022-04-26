<?php

namespace App\Tests\Unit\Service\User;

use App\Entity\User;
use App\Exception\User\UserNotFoundException;
use App\Service\User\ResetPasswordService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class ResetPasswordServiceTest extends UserServiceTestBase
{
    private ResetPasswordService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new ResetPasswordService($this->userRepository, $this->encoderService);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testResetPassword(): void
    {
        $resetPasswordToken = 'abcde';
        $password = 'new-password';
        $user = new User('name', 'user@api.com');

        $this->userRepository
            ->expects($this->exactly(1))
            ->method('findOneByIdAndResetPasswordToken')
            ->with($user->getId(), $resetPasswordToken)
            ->willReturn($user);

        $user = $this->service->reset($user->getId(), $resetPasswordToken, $password);

        self::assertInstanceOf(User::class, $user);
        self::assertNull($user->getResetPasswordToken());
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testResetPasswordForNonExistingUser(): void
    {
        $resetPasswordToken = 'abcde';
        $password = 'new-password';
        $user = new User('name', 'user@api.com');

        $this->userRepository
            ->expects($this->exactly(1))
            ->method('findOneByIdAndResetPasswordToken')
            ->with($user->getId(), $resetPasswordToken)
            ->willThrowException(new UserNotFoundException());

        self::expectException(UserNotFoundException::class);

        $this->service->reset($user->getId(), $resetPasswordToken, $password);
    }
}