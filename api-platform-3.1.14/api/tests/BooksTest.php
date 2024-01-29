<?php
// api/tests/BooksTest.php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Book;
use App\Entity\User;
use App\Factory\BookFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class BooksTest extends ApiTestCase
{
    // This trait provided by Foundry will take care of refreshing the database content to a known state before each test
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
        $client->request('GET', '/api/books', [
            'headers' => [
                'Authorization' => "Bearer $token",
            ],
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/contexts/Book',
            '@id' => '/api/books',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 100,
        ]);

        $this->assertMatchesResourceCollectionJsonSchema(Book::class);
    }

    public function testCreateBook(): void
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
        $client->setDefaultOptions(['headers' => ['Authorization' => "Bearer $token"]]);

        $client->request('POST', '/api/books', ['json' => [
            'isbn' => '0099740915',
            'title' => 'The Handmaid\'s Tale',
            'description' => 'Brilliantly conceived and executed, this powerful evocation of twenty-first century America gives full rein to Margaret Atwood\'s devastating irony, wit and astute perception.',
            'author' => 'Margaret Atwood',
            'publicationDate' => '1985-07-31T00:00:00+00:00',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/contexts/Book',
            '@type' => 'Book',
            'isbn' => '0099740915',
            'title' => 'The Handmaid\'s Tale',
            'description' => 'Brilliantly conceived and executed, this powerful evocation of twenty-first century America gives full rein to Margaret Atwood\'s devastating irony, wit and astute perception.',
            'author' => 'Margaret Atwood',
            'publicationDate' => '1985-07-31T00:00:00+00:00',
            'reviews' => [],
        ]);
    }

    public function testUpdateBook(): void
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

        $bookIdToUpdate = 211;
        $client->request('PUT', "/api/books/$bookIdToUpdate", [
            'headers' => [
                'Authorization' => "Bearer $token",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'title' => 'updated title',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => "/api/books/$bookIdToUpdate",
            'title' => 'updated title',
        ]);
    }

    public function testDeleteBook(): void
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
        $bookIdToDelete = 211;
        $client->request('DELETE', "/api/books/$bookIdToDelete", [
            'headers' => [
                'Authorization' => "Bearer $token",
            ],
        ]);

        // Assert the response
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            static::getContainer()->get('doctrine')->getRepository(Book::class)->find($bookIdToDelete)
        );
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
