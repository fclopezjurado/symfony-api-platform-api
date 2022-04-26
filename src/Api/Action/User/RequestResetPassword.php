<?php

namespace App\Api\Action\User;

use App\Service\Request\RequestService;
use App\Service\User\RequestResetPasswordService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RequestResetPassword
{
    private const RESET_PASSWORD_RESPONSE_MESSAGE = 'Request reset password email sent';

    private RequestResetPasswordService $resetPasswordService;

    public function __construct(RequestResetPasswordService $resetPasswordService)
    {
        $this->resetPasswordService = $resetPasswordService;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $email = RequestService::getField($request, 'email');
        $this->resetPasswordService->send($email);

        return new JsonResponse(['message' => self::RESET_PASSWORD_RESPONSE_MESSAGE]);
    }
}
