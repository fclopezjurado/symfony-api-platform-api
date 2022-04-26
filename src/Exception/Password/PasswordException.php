<?php

namespace App\Exception\Password;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PasswordException extends BadRequestHttpException
{
    private const INVALID_LENGTH_MESSAGE = 'Password must be at least 6 characters';
    private const OLD_PASSWORD_DOES_NOT_MATCH_MESSAGE = 'Old password does not match';

    public static function invalidLength(): self
    {
        throw new self(self::INVALID_LENGTH_MESSAGE);
    }

    public static function oldPasswordDoesNotMatch(): self
    {
        throw new self(self::OLD_PASSWORD_DOES_NOT_MATCH_MESSAGE);
    }
}
