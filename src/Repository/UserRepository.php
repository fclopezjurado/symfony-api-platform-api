<?php

namespace App\Repository;

use App\Entity\User;
use App\Exception\User\UserNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class UserRepository extends BaseRepository
{
    protected static function getEntityClass(): string
    {
        return User::class;
    }

    public function findOneByEmailOrFail(string $email): User
    {
        if (null === $user = $this->objectRepository->findOneBy(['email' => $email])) {
            throw UserNotFoundException::fromEmail($email);
        }

        return $user;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(User $user): void
    {
        $this->saveEntity($user);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(User $user): void
    {
        $this->removeEntity($user);
    }

    public function findOneInactiveByIdAndTokenOrFail(string $id, string $token): User
    {
        if (null === $user = $this->objectRepository->findOneBy(['id' => $id, 'token' => $token, 'active' => false])) {
            throw UserNotFoundException::fromUserIdAndToken($id, $token);
        }

        return $user;
    }

    public function findOneByIdAndResetPasswordToken(string $id, string $resetPasswordToken): User
    {
        $user = $this->objectRepository->findOneBy(['id' => $id, 'resetPasswordToken' => $resetPasswordToken]);

        if (null === $user) {
            throw UserNotFoundException::fromUserIdAndResetPasswordToken($id, $resetPasswordToken);
        }

        return $user;
    }

    public function findOneById(string $id): User
    {
        if (null === $user = $this->objectRepository->find($id)) {
            UserNotFoundException::fromUserId($id);
        }

        return $user;
    }
}
