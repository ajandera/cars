<?php
namespace App\Service;

use App\Repository\CustomerRepository;

class CarCustomerService
{
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $cr)
    {
        $this->customerRepository = $cr;
    }

    public function getCustomerCarData(string $hexId): ?array
    {
        $res = $this->customerRepository->findCustomerWithCarByHex($hexId);
        if (!$res) {
            return null;
        }

        $customer = $res['customer'];
        $car = $res['car'];

        // validation
        if (!$customer->getEmail() || !$car->getMake() || !$car->getModel()) {
            return null;
        }

        return [
            'full_name' => $customer->getFullName(),
            'phone' => $customer->getPhoneNumber(),
            'email' => $customer->getEmail(),
            'country' => $customer->getAddressCountry(),
            'account_status' => $customer->getAccountStatus(),
            'car_type' => $car->getMake() . ' ' . $car->getModel(),
            'car_year' => $car->getYear(),
            'vehicle_condition' => $car->getVehicleCondition(),
            'price_eur' => $car->getPriceEur() !== null ? (float)$car->getPriceEur() : null,
            'last_service_date' => $car->getLastServiceDate()?->format('Y-m-d'),
        ];
    }
}
