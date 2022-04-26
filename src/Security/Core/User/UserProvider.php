<?php

namespace App\Security\Core\User;

use App\Entity\User;
use App\Exception\User\UserNotFoundException;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    private const USERNAME_NOT_FOUND_MESSAGE = 'User %s not found';
    private const UNSUPPORTED_USER_MESSAGE = 'Instances of %s are not supported';

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        try {
            return $this->userRepository->findOneByEmailOrFail($username);
        } catch (UserNotFoundException $exception) {
            throw new UsernameNotFoundException(sprintf(self::USERNAME_NOT_FOUND_MESSAGE, $username));
        }
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf(self::UNSUPPORTED_USER_MESSAGE, get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        $user->setPassword($newEncodedPassword);
        $this->userRepository->save($user);
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }
}
