<?php

namespace App\Exception\User;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserNotFoundException extends NotFoundHttpException
{
    private const FROM_EMAIL_MESSAGE = 'User with email %s not found';
    public const FROM_USER_ID_AND_TOKEN_MESSAGE = 'User with id %s an token %s not found';
    private const FROM_ID_AND_RESET_PASSWORD_TOKEN_MESSAGE = 'User with id %s and reset password token %s not found';
    private const FROM_ID_MESSAGE = 'User with id %s not found';

    public static function fromEmail(string $email): self
    {
        throw new self(sprintf(self::FROM_EMAIL_MESSAGE, $email));
    }

    public static function fromUserIdAndToken(string $id, string $token): self
    {
        throw new self(sprintf(self::FROM_USER_ID_AND_TOKEN_MESSAGE, $id, $token));
    }

    public static function fromUserIdAndResetPasswordToken(string $id, string $resetPasswordToken): self
    {
        throw new self(sprintf(self::FROM_ID_AND_RESET_PASSWORD_TOKEN_MESSAGE, $id, $resetPasswordToken));
    }

    public static function fromUserId(string $id): self
    {
        throw new self(sprintf(self::FROM_ID_MESSAGE, $id));
    }
}
