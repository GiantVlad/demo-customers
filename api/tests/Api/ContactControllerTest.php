<?php

namespace App\Tests\Api;

use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class ContactControllerTest extends WebTestCase
{
    use RefreshDatabaseTrait;

    public function testGetContact()
    {
        $response = static::createClient()->request('GET', '/api/contact/1');

        $this->assertResponseIsSuccessful();
    }
}
