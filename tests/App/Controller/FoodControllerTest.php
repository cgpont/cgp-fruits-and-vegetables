<?php

namespace App\Tests\Controller;

use App\Controller\FoodController;
use App\Service\FoodService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class FoodControllerTest extends WebTestCase
{
    private $foodService;
    private $foodController;

    protected function setUp(): void
    {
        // Mock the FoodService dependency
        $this->foodService = $this->createMock(FoodService::class);

        // Instantiate the FoodController with the mocked FoodService
        $this->foodController = new FoodController($this->foodService);
    }

    public function testListReturnsAllFruitsByDefault()
    {
        // Mock data returned by FoodService
        $mockResults = [
            ['id' => 1, 'name' => 'Apple', 'quantity' => 2000, 'unit' => 'g'],
            ['id' => 2, 'name' => 'Banana', 'quantity' => 1000, 'unit' => 'g']
        ];

        // Configure the mocked service to return $mockResults for getItems
        $this->foodService->expects($this->once())
            ->method('getItems')
            ->with('fruits', '', 'g')
            ->willReturn($mockResults);

        // Create a request with no parameters (defaults will apply)
        $request = new Request();

        // Call the list method
        $response = $this->foodController->list($request);

        // Assert that the response is a JsonResponse with the expected data
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($mockResults, json_decode($response->getContent(), true));
    }

    public function testListFiltersByName()
    {
        // Mock data returned by FoodService when searching for "Apple"
        $mockResults = [
            ['id' => 1, 'name' => 'Apple', 'quantity' => 2000, 'unit' => 'g']
        ];

        // Configure the mocked service to return filtered results
        $this->foodService->expects($this->once())
            ->method('getItems')
            ->with('fruits', 'Apple', 'g')
            ->willReturn($mockResults);

        // Create a request with the name filter
        $request = new Request(['name' => 'Apple']);

        // Call the list method
        $response = $this->foodController->list($request);

        // Assert that the response is a JsonResponse with the expected filtered data
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($mockResults, json_decode($response->getContent(), true));
    }

    public function testListConvertsToKilograms()
    {
        // Mock data with quantities in kilograms
        $mockResultsInKg = [
            ['id' => 1, 'name' => 'Apple', 'quantity' => 2, 'unit' => 'kg'],
            ['id' => 2, 'name' => 'Banana', 'quantity' => 1, 'unit' => 'kg']
        ];

        // Configure the mocked service to return results converted to kilograms
        $this->foodService->expects($this->once())
            ->method('getItems')
            ->with('fruits', '', 'kg')
            ->willReturn($mockResultsInKg);

        // Create a request with the unit set to kg
        $request = new Request(['unit' => 'kg']);

        // Call the list method
        $response = $this->foodController->list($request);

        // Assert that the response contains quantities converted to kilograms
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($mockResultsInKg, json_decode($response->getContent(), true));
    }
}
