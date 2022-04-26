<?php

namespace App\Tests\Functional;

use App\Repository\UserRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\DBAL\Exception as DBALException;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TestBase extends WebTestCase
{
    use FixturesTrait;
    use RecreateDatabaseTrait;

    protected static ?KernelBrowser $client = null;
    protected static ?KernelBrowser $peter = null;
    protected static ?KernelBrowser $brian = null;
    protected static ?KernelBrowser $roger = null;

    protected function setUp(): void
    {
        parent::setUp();

        if (null === self::$client) {
            self::$client = static::createClient();
            self::$client->setServerParameters([
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ]);
        }

        if (null === self::$peter) {
            self::$peter = clone self::$client;
            $this->createAuthenticatedUser(self::$peter, 'peter@api.com');
        }

        if (null === self::$brian) {
            self::$brian = clone self::$client;
            $this->createAuthenticatedUser(self::$brian, 'peter@api.com');
        }

        if (null === self::$roger) {
            self::$roger = clone self::$client;
            $this->createAuthenticatedUser(self::$roger, 'peter@api.com');
        }
    }

    private function createAuthenticatedUser(KernelBrowser &$client, string $email): void
    {
        $user = $this->getContainer()->get(UserRepository::class)->findOneByEmailOrFail($email);
        $token = $this->getContainer()->get(JWTTokenManagerInterface::class)->create($user);

        $client->setServerParameters([
            'HTTP_Authorization' => sprintf('Bearer %s', $token),
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/ld+json',
        ]);
    }

    protected function getResponseData(Response $response): array
    {
        return json_decode($response->getContent(), true);
    }

    protected function initDbConnection(): Connection
    {
        return $this->getContainer()->get('doctrine')->getConnection();
    }

    /**
     * @return false|mixed
     *
     * @throws DBALDriverException
     * @throws DBALException
     */
    protected function getPeterId()
    {
        return $this->initDbConnection()
            ->executeQuery('SELECT id FROM user WHERE email = "peter@api.com"')
            ->fetchOne();
    }

    /**
     * @return false|mixed
     *
     * @throws DBALDriverException
     * @throws DBALException
     */
    protected function getBrianId()
    {
        return $this->initDbConnection()
            ->executeQuery('SELECT id FROM user WHERE email = "brian@api.com"')
            ->fetchOne();
    }
}
