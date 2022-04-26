<?php

namespace App\Tests\Functional\User;

use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class LoginActionTest extends UserTestBase
{
    public function testLogin(): void
    {
        $payload = [
            'username' => 'peter@api.com',
            'password' => 'password',
        ];

        self::$peter->request(
            Request::METHOD_POST,
            sprintf('%s/login_check', $this->endpoint),
            [],
            [],
            [],
            json_encode($payload)
        );

        $response = self::$peter->getResponse();

        self::assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        self::assertInstanceOf(JWTAuthenticationSuccessResponse::class, $response);
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $payload = [
            'username' => 'peter@api.com',
            'password' => 'invalid-password',
        ];

        self::$peter->request(
            Request::METHOD_POST,
            sprintf('%s/login_check', $this->endpoint),
            [],
            [],
            [],
            json_encode($payload)
        );

        $response = self::$peter->getResponse();

        self::assertEquals(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
        self::assertInstanceOf(JWTAuthenticationFailureResponse::class, $response);
    }
}