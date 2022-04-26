<?php

namespace App\Api\Action\User;

use App\Service\Request\RequestService;
use App\Service\User\ResendActivationEmailService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ResendActivationEmail
{
    private const ACTIVATION_EMAIL_RESPONSE_MESSAGE = 'Activation email sent';

    private ResendActivationEmailService $resendActivationEmailService;

    public function __construct(ResendActivationEmailService $resendActivationEmailService)
    {
        $this->resendActivationEmailService = $resendActivationEmailService;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $email = RequestService::getField($request, 'email');
        $this->resendActivationEmailService->resend($email);

        return new JsonResponse(['message' => self::ACTIVATION_EMAIL_RESPONSE_MESSAGE]);
    }
}
