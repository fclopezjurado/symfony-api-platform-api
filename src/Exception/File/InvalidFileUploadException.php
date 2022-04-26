<?php

namespace App\Exception\File;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InvalidFileUploadException extends BadRequestHttpException
{
    private const INVALID_UPLOAD_MESSAGE = 'Cannot get file with input name %s';

    public static function invalidUpload(string $inputName): self
    {
        throw new self(sprintf(self::INVALID_UPLOAD_MESSAGE, $inputName));
    }
}