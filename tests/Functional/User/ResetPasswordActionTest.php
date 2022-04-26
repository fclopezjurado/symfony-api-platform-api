<?php

namespace App\Tests\Functional\User;

use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\DBAL\Exception as DBALException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ResetPasswordActionTest extends UserTestBase
{
    /**
     * @throws DBALDriverException
     * @throws DBALException
     */
    public function testResetPassword(): void
    {
        $peterId = $this->getPeterId();
        $payload = [
            'resetPasswordToken' => '123456',
            'password' => 'new-password',
        ];

        self::$peter->request(
            Request::METHOD_PUT,
            sprintf('%s/%s/reset_password', $this->endpoint, $peterId),
            [],
            [],
            [],
            json_encode($payload)
        );

        $response = self::$peter->getResponse();
        $responseData = $this->getResponseData($response);

        self::assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        self::assertEquals($peterId, $responseData['id']);
    }
}