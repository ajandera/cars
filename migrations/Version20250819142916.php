<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250819142916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create car and customer tables (PostgreSQL syntax)';
    }

    public function up(Schema $schema): void
    {
        // --- CAR TABLE ---
        $this->addSql("CREATE TABLE car (
            id SERIAL PRIMARY KEY,
            hex_id VARCHAR(32) NOT NULL,
            make VARCHAR(50) NOT NULL,
            model VARCHAR(50) NOT NULL,
            year INTEGER NOT NULL,
            vin VARCHAR(255) NOT NULL,
            color VARCHAR(30) DEFAULT NULL,
            mileage INTEGER DEFAULT NULL,
            engine_type VARCHAR(50) DEFAULT NULL,
            engine_displacement_cc INTEGER DEFAULT NULL,
            horsepower INTEGER DEFAULT NULL,
            torque_nm INTEGER DEFAULT NULL,
            transmission VARCHAR(30) NOT NULL,
            drivetrain VARCHAR(10) NOT NULL,
            fuel_type VARCHAR(30) NOT NULL,
            body_style VARCHAR(50) DEFAULT NULL,
            doors SMALLINT DEFAULT NULL,
            seats SMALLINT DEFAULT NULL,
            price_eur NUMERIC(10,2) DEFAULT NULL,
            listing_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
            vehicle_condition VARCHAR(30) NOT NULL,
            has_sunroof BOOLEAN NOT NULL DEFAULT FALSE,
            has_navigation BOOLEAN NOT NULL DEFAULT FALSE,
            has_heated_seats BOOLEAN NOT NULL DEFAULT FALSE,
            interior_material VARCHAR(50) DEFAULT 'Cloth',
            last_service_date DATE DEFAULT NULL
        );");

        // --- CUSTOMER TABLE ---
        $this->addSql("CREATE TABLE customer (
            id SERIAL PRIMARY KEY,
            car_hex_id VARCHAR(32) NOT NULL,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone_number VARCHAR(25) DEFAULT NULL,
            address_street VARCHAR(255) DEFAULT NULL,
            address_city VARCHAR(100) DEFAULT NULL,
            address_state VARCHAR(100) DEFAULT NULL,
            address_postal_code VARCHAR(20) DEFAULT NULL,
            address_country VARCHAR(50) DEFAULT NULL,
            date_of_birth DATE DEFAULT NULL,
            registration_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
            last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            account_status VARCHAR(20) NOT NULL DEFAULT 'active',
            newsletter_subscribed BOOLEAN NOT NULL DEFAULT FALSE
        );");

        // --- EXAMPLE DATA ---
        // ⚠️ Insert statements musí mít správné typy (booleany jako true/false)
        $this->addSql("INSERT INTO car (id, hex_id, make, model, year, vin, color, mileage, engine_type, engine_displacement_cc, horsepower, torque_nm, transmission, drivetrain, fuel_type, body_style, doors, seats, price_eur, listing_date, vehicle_condition, has_sunroof, has_navigation, has_heated_seats, interior_material, last_service_date)
            VALUES
            (1, 'a1b2c3d4e5f67890', 'Toyota', 'Camry', 2021, 'VIN000000000000001', 'Supersonic Red', 15000, '2.5L I4', 2487, 203, 250, 'Automatic', 'FWD', 'Gasoline', 'Sedan', 4, 5, 28000.00, NOW(), 'Used', true, true, true, 'Leather', '2023-05-10'),
            (2, 'f0e9d8c7b6a54321', 'Ford', 'Mustang', 2022, 'VIN000000000000002', 'Shadow Black', 8000, '5.0L V8', 4951, 450, 556, 'Manual', 'RWD', 'Gasoline', 'Coupe', 2, 4, 45000.00, NOW(), 'Used', false, true, true, 'Leather', '2023-06-15')
        ;");

        $this->addSql("INSERT INTO customer (id, car_hex_id, first_name, last_name, email, phone_number, address_country, account_status, newsletter_subscribed, registration_date)
            VALUES
            (1, 'a1b2c3d4e5f67890', 'John', 'Doe', 'john.doe@example.com', '+1-202-555-0101', 'USA', 'active', false, NOW()),
            (2, 'f0e9d8c7b6a54321', 'Jane', 'Smith', 'jane.smith@example.com', '+44-20-7946-0102', 'UK', 'active', false, NOW())
        ;");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS customer');
        $this->addSql('DROP TABLE IF EXISTS car');
    }
}
