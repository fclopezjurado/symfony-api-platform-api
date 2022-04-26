<?php

namespace App\Tests\Unit\Service\User;

use App\Repository\UserRepository;
use App\Service\Password\EncoderService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBusInterface;

abstract class UserServiceTestBase extends TestCase
{
    /** @var UserRepository|MockObject $userRepository */
    protected $userRepository;

    /** @var EncoderService|MockObject $encoderService */
    protected $encoderService;

    /** @var MessageBusInterface|MockObject */
    protected $messageBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->encoderService = $this->getMockBuilder(EncoderService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->messageBus = $this->getMockBuilder(MessageBusInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}