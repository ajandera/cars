<?php
namespace App\Controller;

use App\Service\CarCustomerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CarController extends AbstractController
{
    private CarCustomerService $service;

    public function __construct(CarCustomerService $service)
    {
        $this->service = $service;
    }

    #[Route('/car/{hex_id}', name: 'car_info', methods: ['POST'])]
    public function carInfo(string $hex_id, Request $request): JsonResponse
    {
        $data = $this->service->getCustomerCarData($hex_id);
        if (!$data) {
            return $this->json(['error' => 'Customer or car not found'], 404);
        }

        return $this->json($data);
    }

    #[Route('/{anything}', name: 'car_other', methods: ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'])]
    public function otherPaths(): JsonResponse
    {
        return $this->json(['error' => 'No data available'], 404);
    }
}
