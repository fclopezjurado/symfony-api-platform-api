<?php

namespace App\Service\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\File\FileService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\Request;

class UploadAvatarService
{
    private UserRepository $userRepository;
    private FileService $fileService;

    public function __construct(UserRepository $userRepository, FileService $fileService)
    {
        $this->userRepository = $userRepository;
        $this->fileService = $fileService;
    }

    /**
     * @param Request $request
     * @param User $user
     * @return User
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function uploadAvatar(Request $request, User $user): User
    {
        $file = $this->fileService->validateFile($request, FileService::AVATAR_INPUT_NAME);

        $this->fileService->deleteFile($user->getAvatar());

        $filename = $this->fileService->uploadFile($file, FileService::AVATAR_INPUT_NAME);

        $user->setAvatar($filename);
        $this->userRepository->save($user);

        return $user;
    }
}