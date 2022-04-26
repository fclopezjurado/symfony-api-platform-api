<?php

namespace App\Tests\Unit\Service\User;

use App\Entity\User;
use App\Exception\User\UserNotFoundException;
use App\Service\User\ActivateAccountService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Uid\Uuid;

class ActivateAccountServiceTest extends UserServiceTestBase
{
    private ActivateAccountService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new ActivateAccountService($this->userRepository);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testActivateAccount(): void
    {
        $user = new User('user', 'user@email.com');
        $id = Uuid::v4()->toRfc4122();
        $token = sha1(uniqid());

        $this->userRepository
            ->expects($this->exactly(1))
            ->method('findOneInactiveByIdAndTokenOrFail')
            ->with($id, $token)
            ->willReturn($user);

        $result = $this->service->activate($id, $token);

        self::assertInstanceOf(User::class, $result);
        self::assertNull($user->getToken());
        self::assertTrue($user->isActive());
    }

    public function testForNonExistingUser(): void
    {
        $id = Uuid::v4()->toRfc4122();
        $token = sha1(uniqid());
        $exceptionMessage = sprintf(UserNotFoundException::FROM_USER_ID_AND_TOKEN_MESSAGE, $id, $token);

        $this->userRepository
            ->expects($this->exactly(1))
            ->method('findOneInactiveByIdAndTokenOrFail')
            ->with($id, $token)
            ->willThrowException(new UserNotFoundException($exceptionMessage));

        self::expectException(UserNotFoundException::class);
        self::expectExceptionMessage($exceptionMessage);

        $this->service->activate($id, $token);
    }
}