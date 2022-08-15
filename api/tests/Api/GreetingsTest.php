<?php

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Contact;

class ContactControllerTest extends ApiTestCase
{
    public function testCreateGreeting()
    {
        $response = static::createClient()->request('GET', '/greetings', ['json' => [
            'name' => 'Kévin',
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '@context' => '/contexts/Contact',
            '@type' => 'Contact',
            'name' => 'Kévin',
        ]);
    }
}
