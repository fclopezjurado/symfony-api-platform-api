<?php

namespace App\Tests\Functional\User;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RegisterActionTest extends UserTestBase
{
    public function testRegister(): void
    {
        $payload = [
            'name' => 'Stewie',
            'email' => 'stewie@api.com',
            'password' => '123456',
        ];

        self::$client->request(
            Request::METHOD_POST,
            sprintf('%s/register', $this->endpoint),
            [],
            [],
            [],
            json_encode($payload)
        );

        $response = self::$client->getResponse();
        $responseData = $this->getResponseData($response);

        self::assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
        self::assertEquals($payload['email'], $responseData['email']);
    }

    public function testRegisterWithMissingParameters(): void
    {
        $payload = [
            'name' => 'Stewie',
            'password' => '123456',
        ];

        self::$client->request(
            Request::METHOD_POST,
            sprintf('%s/register', $this->endpoint),
            [],
            [],
            [],
            json_encode($payload)
        );

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testRegisterWithInvalidPassword(): void
    {
        $payload = [
            'name' => 'Stewie',
            'email' => 'stewie@api.com',
            'password' => '1',
        ];

        self::$client->request(
            Request::METHOD_POST,
            sprintf('%s/register', $this->endpoint),
            [],
            [],
            [],
            json_encode($payload)
        );

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}