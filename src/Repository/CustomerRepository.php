<?php
namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Car;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

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
            ->select("
                CONCAT(c.first_name, ' ', c.last_name) AS full_name,
                c.phone_number AS phone,
                c.email AS email,
                c.address_country AS country,
                c.account_status AS account_status,
                CONCAT(car.make, ' ', car.model) AS car_type,
                car.year AS car_year,
                car.vehicle_condition AS vehicle_condition,
                car.price_eur AS price_eur,
                car.last_service_date AS last_service_date
            ")
            ->innerJoin(Car::class, 'car', 'WITH', 'car.hex_id = c.car_hex_id')
            ->andWhere('car.hex_id = :hex')
            ->setParameter('hex', $hexId)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }
}
