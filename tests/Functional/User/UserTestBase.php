<?php

namespace App\Tests\Functional\User;

use App\Tests\Functional\TestBase;

abstract class UserTestBase extends TestBase
{
    protected string $endpoint;

    protected function setUp(): void
    {
        parent::setUp();

        $this->endpoint = '/api/v1/users';
    }
}