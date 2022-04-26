<?php

namespace App\Tests\Functional\User;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RequestResetPasswordActionTest extends UserTestBase
{
    public function testRequestResetPassword(): void
    {
        $payload = ['email' => 'peter@api.com'];

        self::$peter->request(
            Request::METHOD_POST,
            sprintf('%s/request_reset_password', $this->endpoint),
            [],
            [],
            [],
            json_encode($payload)
        );

        $response = self::$peter->getResponse();

        self::assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    public function testRequestResetPasswordForNonExistingEmail(): void
    {
        $payload = ['email' => 'non-existing@api.com'];

        self::$peter->request(
            Request::METHOD_POST,
            sprintf('%s/request_reset_password', $this->endpoint),
            [],
            [],
            [],
            json_encode($payload)
        );

        $response = self::$peter->getResponse();

        self::assertEquals(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}