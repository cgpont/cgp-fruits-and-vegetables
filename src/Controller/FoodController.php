<?php

namespace App\Controller;

use App\Service\FoodService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FoodController extends AbstractController
{
    private FoodService $foodService;

    public function __construct(FoodService $foodService)
    {
        $this->foodService = $foodService;
    }

    /**
     * @Route("/api/food", methods={"GET"})
     */
    public function list(Request $request): JsonResponse
    {
        $type = $request->query->get('type', 'fruits');
        $name = $request->query->get('name', '');
        $unit = $request->query->get('unit', 'g');

        $results = $this->foodService->getItems($type, $name, $unit);

        return new JsonResponse($results);
    }

    /**
     * @Route("/api/food", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $result = $this->foodService->addItem($data);

        return new JsonResponse($result, JsonResponse::HTTP_CREATED);
    }
}
