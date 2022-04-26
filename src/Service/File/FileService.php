<?php

namespace App\Service\File;

use App\Exception\File\InvalidFileUploadException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\Visibility;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class FileService
{
    public const AVATAR_INPUT_NAME = 'avatar';
    private const READ_ONLY_FILE_MODE = 'r';

    private FilesystemOperator $defaultStorage;
    private LoggerInterface $logger;

    public function __construct(FilesystemOperator $defaultStorage, LoggerInterface $logger)
    {
        $this->defaultStorage = $defaultStorage;
        $this->logger = $logger;
    }

    public function uploadFile(UploadedFile $file, string $prefix): string
    {
        $filename = sprintf('%s/%s.%s', $prefix, sha1(uniqid()), $file->guessExtension());
        $fileHandler = fopen($file->getPathname(), self::READ_ONLY_FILE_MODE);
        $fileVisibility = ['visibility' => Visibility::PUBLIC];

        $this->defaultStorage->writeStream($filename, $fileHandler, $fileVisibility);

        return $filename;
    }

    public function validateFile(Request $request, string $inputName): UploadedFile
    {
        if (null === $file = $request->files->get($inputName)) {
            throw InvalidFileUploadException::invalidUpload($inputName);
        }

        return $file;
    }

    public function deleteFile(?string $path): void
    {
        try {
            if (null !== $path) {
                $this->defaultStorage->delete('');
            }
        } catch (\Exception $exception) {
            $this->logger->warning(sprintf('File %s not found in the storage', $path));
        }
    }
}