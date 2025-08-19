<?php
namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Car;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    /**
     * @param string $hexId
     * @return array|null
     */
    public function findCustomerWithCarByHex(string $hexId): ?array
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c', 'car')
            ->innerJoin(Car::class, 'car', 'WITH', 'car.hex_id = c.car_hex_id')
            ->andWhere('c.car_hex_id = :hex')
            ->setParameter('hex', $hexId)
            ->setMaxResults(1);

        $res = $qb->getQuery()->getResult();
        if (!$res) {
            return null;
        }

        // Doctrine return array need to be unified
        if (is_array($res)) {
            $customer = $res[0] ?? $res['c'] ?? null;
            $car = $res[1] ?? $res['car'] ?? null;
        } else {
            return null;
        }

        if (!$customer || !$car) {
            return null;
        }

        return [
            'customer' => $customer,
            'car' => $car,
        ];
    }
}
