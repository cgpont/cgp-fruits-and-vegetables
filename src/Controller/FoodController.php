<?php

namespace App\Controller;

use App\Service\FoodService;
use App\Service\FoodInputValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FoodController extends AbstractController
{
    private FoodService $foodService;
    private FoodInputValidator $validator;

    public function __construct(FoodService $foodService, FoodInputValidator $validator)
    {
        $this->foodService = $foodService;
        $this->validator = $validator;
    }

    /**
     * @Route("/api/food", methods={"GET"})
     */
    public function list(Request $request): JsonResponse
    {
        $type = $request->query->get('type');
        if (!in_array($type, ['fruits', 'vegetables'])) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Type must be either "fruits" or "vegetables".'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        
        $unit = $request->query->get('unit', 'g');
        if (!in_array($unit, ['g', 'kg'])) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Type must be either "g" or "kg".'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }        

        $name = $request->query->get('name', '');
        
        $results = $this->foodService->getItems($type, $name, $unit);

        return new JsonResponse($results);
    }

    /**
     * @Route("/api/food", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $validationResult = $this->validator->validateItemInput($data);

        if (!$validationResult['valid']) {
            return new JsonResponse([
                'status' => 'error',
                'errors' => $validationResult['errors'],
            ], JsonResponse::HTTP_BAD_REQUEST);
        }        

        $result = $this->foodService->addItem($data);
        return new JsonResponse($result, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/food/{type}/{id}", methods={"DELETE"})
     */
    public function remove(string $type, int $id): JsonResponse
    {
        if (!in_array($type, ['fruits', 'vegetables'])) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Type must be either "fruits" or "vegetables".'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->foodService->removeItem($type, $id);
        return new JsonResponse(['status' => 'success', 'message' => "Item with ID $id removed from $type"], JsonResponse::HTTP_OK);
    }    
}
