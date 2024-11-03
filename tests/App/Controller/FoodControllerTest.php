<?php

namespace App\Tests\Controller;

use App\Config\AppConfig;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class FoodControllerTest extends WebTestCase
{
    // Test for the GET /api/food endpoint with valid and invalid inputs
    public function testListWithInvalidType()
    {
        $client = static::createClient();
        $client->request('GET', '/api/food?type=invalid_type');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('error', $responseData['status']);
        $this->assertEquals('Type must be one of: ' . implode(', ', AppConfig::VALID_TYPES) . '.', $responseData['message']);
    }

    public function testListWithInvalidUnit()
    {
        $client = static::createClient();
        $client->request('GET', '/api/food?type=fruits&unit=invalid_unit');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('error', $responseData['status']);
        $this->assertEquals('Unit must be one of: ' . implode(', ', AppConfig::VALID_UNITS) . '.', $responseData['message']);
    }

    public function testListWithValidTypeAndUnit()
    {
        $client = static::createClient();
        $client->request('GET', '/api/food?type=fruits&unit=g');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $responseData = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertIsArray($responseData);
    }

    // Test for the POST /api/food endpoint with valid and invalid input
    public function testAddItemWithValidInput()
    {
        $client = static::createClient();

        $data = [
            'id' => 1,
            'name' => 'Apple',
            'type' => 'fruit',
            'quantity' => 500,
            'unit' => 'g',
        ];

        $client->request('POST', '/api/food', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($data['id'], $responseData['id']);
    }

    public function testAddItemWithInvalidInput()
    {
        $client = static::createClient();

        $data = [
            'id' => 'invalid_id', // Invalid ID
            'name' => 'Apple',
            'type' => 'fruit',
            'quantity' => 500,
            'unit' => 'g',
        ];

        $client->request('POST', '/api/food', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('errors', $responseData);
    }

    // Test for the DELETE /api/food/{type}/{id} endpoint with valid and invalid types
    public function testRemoveWithValidType()
    {
        $client = static::createClient();

        // Assume ID 1 exists in the database
        $client->request('DELETE', '/api/food/fruits/1');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('success', $responseData['status']);
        $this->assertStringContainsString('Item with ID 1 removed from fruits', $responseData['message']);
    }

    public function testRemoveWithInvalidType()
    {
        $client = static::createClient();

        $client->request('DELETE', '/api/food/invadlid_type/1');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('error', $responseData['status']);
        $this->assertEquals('Type must be one of: ' . implode(', ', AppConfig::VALID_TYPES) . '.', $responseData['message']);
    }

    public function testRemoveNonExistentItem()
    {
        $client = static::createClient();

        $client->request('DELETE', '/api/food/fruits/999');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());

        $this->assertJson($client->getResponse()->getContent());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('error', $responseData['status']);
        $this->assertStringContainsString('Item with ID 999 not found', $responseData['message']);
    }
}
