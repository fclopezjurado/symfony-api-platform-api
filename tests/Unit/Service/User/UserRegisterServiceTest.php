<?php

namespace App\Tests\Unit\Service\User;

use App\Entity\User;
use App\Exception\Password\PasswordException;
use App\Exception\User\UserAlreadyExistsException;
use App\Messenger\Message\UserRegisteredMessage;
use App\Service\User\UserRegisterService;
use Doctrine\ORM\ORMException;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\Messenger\Envelope;

class UserRegisterServiceTest extends UserServiceTestBase
{
    private UserRegisterService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new UserRegisterService($this->userRepository, $this->encoderService, $this->messageBus);
    }

    public function testUserRegister(): void
    {
        $name = 'username';
        $email = 'username@api.com';
        $password = '123456';
        $message = $this->getMockBuilder(UserRegisteredMessage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageBus
            ->expects($this->exactly(1))
            ->method('dispatch')
            ->with($this->isType('object'), $this->isType('array'))
            ->willReturn(new Envelope($message));

        $user = $this->service->create($name, $email, $password);

        self::assertInstanceOf(User::class, $user);
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($name, $user->getName());
    }

    public function testUserRegisterForInvalidPassword(): void
    {
        $name = 'username';
        $email = 'username@api.com';
        $password = '123';

        $this->encoderService
            ->expects($this->exactly(1))
            ->method('generateEncodedPassword')
            ->with($this->isType('object'), $this->isType('string'))
            ->willThrowException(new PasswordException());

        self::expectException(PasswordException::class);
        $this->service->create($name, $email, $password);
    }

    public function testUserRegisterForAlreadyExistingUser(): void
    {
        $name = 'username';
        $email = 'username@api.com';
        $password = '123';

        $this->userRepository
            ->expects($this->exactly(1))
            ->method('save')
            ->with($this->isType('object'))
            ->willThrowException(new ORMException());

        self::expectException(UserAlreadyExistsException::class);
        $this->service->create($name, $email, $password);
    }
}