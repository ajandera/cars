<?php
namespace App\Controller;

use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CarController extends AbstractController
{
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $cr)
    {
        $this->customerRepository = $cr;
    }

    #[Route('/car/{hex_id}', name: 'car_info', methods: ['POST'], priority: 1)]
    public function carInfo(string $hex_id, Request $request): JsonResponse
    {
        $data = $this->customerRepository->findCustomerWithCarByHex($hex_id);
        
        if (!$data) {
            return $this->json(['error' => 'Customer or car not found'], 404);
        }

        return $this->json($data);
    }
}
