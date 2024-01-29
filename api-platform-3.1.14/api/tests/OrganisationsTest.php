<?php
namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Organisation;
use App\Factory\OrganisationFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class OrganisationsTest extends ApiTestCase
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
        $client->request('GET', '/api/organisations', [
            'headers' => [
                'Authorization' => "Bearer $token",
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/contexts/Organisation',
            '@id' => '/api/organisations',
            '@type' => 'hydra:Collection',
        ]);
        $this->assertMatchesResourceCollectionJsonSchema(Organisation::class);


    }

    public function testCreateOrganisation(): void
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
        $client->request('POST', '/api/organisations', [
            'headers' => [
                'Authorization' => "Bearer $token",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'name' => 'New Organisation Name',

            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $organisationRepository = $entityManager->getRepository(Organisation::class);
        $createdOrganisation = $organisationRepository->findOneBy(['name' => 'New Organisation Name']);
        $this->assertInstanceOf(Organisation::class, $createdOrganisation);


    }

    public function testCreateInvalidOrganisation(): void
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

        $client->request('POST', '/api/organisations', [
            'headers' => [
                'Authorization' => "Bearer $token",
                'Content-Type' => 'application/json',
            ],
            'json' => [

            ],
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

    }

    public function testUpdateOrganisation(): void
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

        $organisationIdToUpdate = 1033;
        $client->request('PUT', "/api/organisations/$organisationIdToUpdate", [
            'headers' => [
                'Authorization' => "Bearer $token",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'name' => 'Updated Organisation Name',

            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => "/api/organisations/$organisationIdToUpdate",
            'name' => 'Updated Organisation Name',

        ]);

    }

    public function testDeleteOrganisation(): void
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

        $organisationIdToDelete = 1033;
        $client->request('DELETE', "/api/organisations/$organisationIdToDelete", [
            'headers' => [
                'Authorization' => "Bearer $token",
            ],
        ]);

        $this->assertResponseStatusCodeSame(204);
    }

    private function getAuthToken(string $username, string $password): string
    {
        $response = static::createClient()->request('POST', '/auth', [
            'json' => [
                'email' => $username,
                'password' => $password,
            ],
        ]);

        $data = $response->toArray();
        return $data['token'];
    }
}
