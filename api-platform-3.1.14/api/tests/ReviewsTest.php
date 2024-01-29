<?php
// api/tests/BooksTest.php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Review;
use App\Factory\ReviewFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use App\Entity\Book;
use App\Factory\BookFactory;
class ReviewsTest extends ApiTestCase
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
        $client->request('GET', '/api/reviews', [
            'headers' => [
                'Authorization' => "Bearer $token",
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testCreateReview(): void
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
        $client->request('POST', '/api/reviews', [
            'headers' => [
                'Authorization' => "Bearer $token",
            ],
            'json' => [
                'rating' => 4,
                'body' => 'A great book!',
                'author' => 'John Doe',
                'publicationDate' => '2023-09-13T00:00:00+00:00',
                'book' => '/api/books/211',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
    public function testCreateInvalidReview(): void
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
        $client->request('POST', '/api/reviews', [
            'headers' => [
                'Authorization' => "Bearer $token",
            ],
            'json' => [

            ],
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

    }

    public function testUpdateReview(): void
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

        $client->request('PUT', '/api/reviews/3402', [
            'headers' => [
                'Authorization' => "Bearer $token",
            ],
            'json' => [
                'rating' => 5,
                'body' => 'An updated review!',
                'publicationDate' => '2023-09-14T00:00:00+00:00',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

    }

    public function testDeleteReview(): void
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
        $client->request('DELETE', '/api/reviews/3402', [
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
