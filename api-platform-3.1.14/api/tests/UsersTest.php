<?php
namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UsersTest extends ApiTestCase
{
    use ResetDatabase, Factories;
    protected function setUp(): void
    {
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
    }

    public function testGetCollection(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/register', [
            'json' => [
                'firstName' => 'Ana',
                'lastName' => 'Maria',
                'email' => 'ana.doe@example.com',
                'plainPassword' => 'ana',
            ],
        ]);
        $token = $this->getAuthToken('ana@example.com', 'ana');
        $client->request('GET', '/api/users', [
            'headers' => [
                'Authorization' => "Bearer $token",
            ],
        ]);

        // Assert the response
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');


    }

    public function testCreateUser(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/register', [
            'json' => [
                'firstName' => 'Ana',
                'lastName' => 'Maria',
                'email' => 'ana.doe@example.com',
                'plainPassword' => 'ana',
            ],
        ]);
        $client->request('POST', '/api/users', [
            'json' => [
                'firstName' => 'John',
                'email' => 'john@example.com',
                'plainPassword' => 'password',
            ],
        ]);


        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testCreateInvalidUser(): void
    {

        $client = static::createClient();
        $client->request('POST', '/api/register', [
            'json' => [
                'firstName' => 'Ana',
                'lastName' => 'Maria',
                'email' => 'ana.doe@example.com',
                'plainPassword' => 'ana',
            ],
        ]);
        $client->request('POST', '/api/users', [
            'json' => [

            ],
        ]);
        $this->assertResponseStatusCodeSame(422);
    }

    public function testUpdateUser(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/register', [
            'json' => [
                'firstName' => 'Ana',
                'lastName' => 'Maria',
                'email' => 'ana.doe@example.com',
                'plainPassword' => 'ana',
            ],
        ]);
        $token = $this->getAuthToken('ana@example.com', 'ana');

        $client->request('PUT', '/api/users/1220', [
            'headers' => [
                'Authorization' => "Bearer $token",
            ],
            'json' => [
                'firstName' => 'Updated Name',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testDeleteUser(): void
    {

        $client = static::createClient();
        $client->request('POST', '/api/register', [
            'json' => [
                'firstName' => 'Ana',
                'lastName' => 'Maria',
                'email' => 'ana.doe@example.com',
                'plainPassword' => 'ana',
            ],
        ]);
        $token = $this->getAuthToken('ana@example.com', 'ana');

        $client->request('DELETE', '/api/users/1220', [
            'headers' => [
                'Authorization' => "Bearer $token",
            ],
        ]);

        $this->assertResponseStatusCodeSame(204);
    }
    private function getAuthToken(string $username, string $password): string
    {
        $client = self::createClient();
        $response = $client->request('POST', '/auth', ['json' => [
            'email' => $username,
            'password' => $password,
        ]]);

        $data = $response->toArray();
        return $data['token'];
    }
}
