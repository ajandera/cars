<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Car;
use App\Entity\Customer;

class CarControllerTest extends WebTestCase
{
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $car = new Car();
        $car->setHexId('testhex123');
        $car->setMake('TestMake');
        $car->setModel('TestModel');
        $car->setYear(2020);
        $car->setPriceEur('10000.00');
        $car->setVehicleCondition('Used');
        $this->entityManager->persist($car);

        $customer = new Customer();
        $customer->setCarHexId('testhex123');
        $customer->setFirstName('Test');
        $customer->setLastName('User');
        $customer->setEmail('test.user@example.com');
        $customer->setPhoneNumber('+420123456789');
        $customer->setAddressCountry('Czech Republic');
        $customer->setAccountStatus('active');
        $this->entityManager->persist($customer);

        $this->entityManager->flush();
    }

    public function testExistingCustomerReturnsJson()
    {
        $client = static::createClient();
        $client->request('POST', '/car/testhex123');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('full_name', $data);
        $this->assertEquals('Test User', $data['full_name']);
        $this->assertEquals('+420123456789', $data['phone']);
    }

    public function testNonExistingCustomerReturns404()
    {
        $client = static::createClient();
        $client->request('POST', '/car/doesnotexist');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // rollback DB changes or drop schema if using in-memory DB
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
