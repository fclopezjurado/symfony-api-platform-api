<?php

namespace App\Tests\Functional\User;

use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\DBAL\Exception as DBALException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ChangePasswordActionTest extends UserTestBase
{
    /**
     * @throws DBALDriverException
     * @throws DBALException
     */
    public function testChangePassword(): void
    {
        $payload = [
            'oldPassword' => 'password',
            'newPassword' => 'new-password',
        ];

        self::$peter->request(
            Request::METHOD_PUT,
            sprintf('%s/%s/change_password', $this->endpoint, $this->getPeterId()),
            [],
            [],
            [],
            json_encode($payload)
        );

        $response = self::$peter->getResponse();

        self::assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @throws DBALDriverException
     * @throws DBALException
     */
    public function testChangePasswordWithInvalidOldPassword(): void
    {
        $payload = [
            'oldPassword' => 'non-a-good-one',
            'newPassword' => 'new-password',
        ];

        self::$peter->request(
            Request::METHOD_PUT,
            sprintf('%s/%s/change_password', $this->endpoint, $this->getPeterId()),
            [],
            [],
            [],
            json_encode($payload)
        );

        $response = self::$peter->getResponse();

        self::assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}