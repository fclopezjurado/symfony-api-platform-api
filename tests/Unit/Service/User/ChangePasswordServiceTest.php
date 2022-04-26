<?php

namespace App\Tests\Unit\Service\User;

use App\Entity\User;
use App\Exception\Password\PasswordException;
use App\Service\User\ChangePasswordService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class ChangePasswordServiceTest extends UserServiceTestBase
{
    private ChangePasswordService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new ChangePasswordService($this->userRepository, $this->encoderService);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testChangePassword(): void
    {
        $user = new User('name', 'name@api.com');
        $oldPassword = 'old-password';
        $newPassword = 'new-password';

        $this->userRepository
            ->expects($this->exactly(1))
            ->method('findOneById')
            ->with($this->isType('string'))
            ->willReturn($user);
        $this->encoderService
            ->expects($this->exactly(1))
            ->method('isValidPassword')
            ->with($user, $oldPassword)
            ->willReturn(true);

        $user = $this->service->changePassword($user->getId(), $oldPassword, $newPassword);

        self::assertInstanceOf(User::class, $user);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testChangePasswordForInvalidOldPassword(): void
    {
        $user = new User('name', 'name@api.com');
        $oldPassword = 'old-password';
        $newPassword = 'new-password';

        $this->userRepository
            ->expects($this->exactly(1))
            ->method('findOneById')
            ->with($this->isType('string'))
            ->willReturn($user);
        $this->encoderService
            ->expects($this->exactly(1))
            ->method('isValidPassword')
            ->with($user, $oldPassword)
            ->willReturn(false);

        self::expectException(PasswordException::class);

        $this->service->changePassword($user->getId(), $oldPassword, $newPassword);
    }
}