<?php

namespace App\Tests\Functional\User;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ResendActivationEmailActionTest extends UserTestBase
{
    public function testResendActivationEmail(): void
    {
        $payload = ['email' => 'roger@api.com',];

        self::$roger->request(
            Request::METHOD_POST,
            sprintf('%s/resend_activation_email', $this->endpoint),
            [],
            [],
            [],
            json_encode($payload)
        );

        $response = self::$roger->getResponse();

        self::assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    public function testResendActivationEmailToActiveUser(): void
    {
        $payload = ['email' => 'peter@api.com',];

        self::$peter->request(
            Request::METHOD_POST,
            sprintf('%s/resend_activation_email', $this->endpoint),
            [],
            [],
            [],
            json_encode($payload)
        );

        $response = self::$peter->getResponse();

        self::assertEquals(JsonResponse::HTTP_CONFLICT, $response->getStatusCode());
    }
}