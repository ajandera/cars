<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CarRepository;

#[ORM\Entity(repositoryClass: CarRepository::class)]
#[ORM\Table(name: "car")]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 32, unique: true)]
    private string $hex_id;

    #[ORM\Column(type: "string", length: 50)]
    private string $make;

    #[ORM\Column(type: "string", length: 50)]
    private string $model;

    #[ORM\Column(type: "integer")]
    private int $year;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2, nullable: true)]
    private ?string $price_eur = null;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private ?string $vehicle_condition = null;

    #[ORM\Column(type: "date", nullable: true)]
    private ?\DateTimeInterface $last_service_date = null;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private ?string $vin = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $mileage = null;

    #[ORM\Column(type: "string", length: 30, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private ?string $engine_type = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $engine_displacement_cc = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $horsepower = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $torque_nm = null;

    #[ORM\Column(type: "string", length: 30)]
    private string $transmission;

    #[ORM\Column(type: "string", length: 10)]
    private string $drivetrain;

    #[ORM\Column(type: "string", length: 30)]
    private string $fuel_type;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private ?string $body_style = null;

    #[ORM\Column(type: "smallint", nullable: true)]
    private ?int $doors = null;

    #[ORM\Column(type: "smallint", nullable: true)]
    private ?int $seats = null;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private \DateTimeInterface $listing_date;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $has_sunroof = false;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $has_navigation = false;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $has_heated_seats = false;

    #[ORM\Column(type: "string", length: 50, nullable: true, options: ["default" => "Cloth"])]
    private ?string $interior_material = 'Cloth';

    // --- Getters/Setters ---
    public function getId(): ?int { return $this->id; }

    public function getHexId(): string { return $this->hex_id; }
    public function setHexId(string $hex): self { $this->hex_id = $hex; return $this; }

    public function getMake(): string { return $this->make; }
    public function setMake(string $m): self { $this->make = $m; return $this; }

    public function getModel(): string { return $this->model; }
    public function setModel(string $m): self { $this->model = $m; return $this; }

    public function getYear(): int { return $this->year; }
    public function setYear(int $y): self { $this->year = $y; return $this; }

    public function getPriceEur(): ?float { return $this->price_eur ? (float)$this->price_eur : null; }
    public function setPriceEur(?float $p): self { 
        $this->price_eur = $p !== null ? number_format($p, 2, '.', '') : null; 
        return $this; 
    }

    public function getVehicleCondition(): ?string { return $this->vehicle_condition; }
    public function setVehicleCondition(?string $c): self { $this->vehicle_condition = $c; return $this; }

    public function getLastServiceDate(): ?\DateTimeInterface { return $this->last_service_date; }
    public function setLastServiceDate(?\DateTimeInterface $d): self { $this->last_service_date = $d; return $this; }

    public function getVin(): ?string { return $this->vin; }
    public function setVin(?string $v): self { $this->vin = $v; return $this; }

    public function getMileage(): ?int { return $this->mileage; }
    public function setMileage(?int $m): self { $this->mileage = $m; return $this; }

    public function getColor(): ?string { return $this->color; }
    public function setColor(?string $color): self { $this->color = $color; return $this; }

    public function getEngineType(): ?string { return $this->engine_type; }
    public function setEngineType(?string $engine_type): self { $this->engine_type = $engine_type; return $this; }

    public function getEngineDisplacementCc(): ?int { return $this->engine_displacement_cc; }
    public function setEngineDisplacementCc(?int $cc): self { $this->engine_displacement_cc = $cc; return $this; }

    public function getHorsepower(): ?int { return $this->horsepower; }
    public function setHorsepower(?int $hp): self { $this->horsepower = $hp; return $this; }

    public function getTorqueNm(): ?int { return $this->torque_nm; }
    public function setTorqueNm(?int $torque): self { $this->torque_nm = $torque; return $this; }

    public function getTransmission(): string { return $this->transmission; }
    public function setTransmission(string $transmission): self { $this->transmission = $transmission; return $this; }

    public function getDrivetrain(): string { return $this->drivetrain; }
    public function setDrivetrain(string $drivetrain): self { $this->drivetrain = $drivetrain; return $this; }

    public function getFuelType(): string { return $this->fuel_type; }
    public function setFuelType(string $fuel): self { $this->fuel_type = $fuel; return $this; }

    public function getBodyStyle(): ?string { return $this->body_style; }
    public function setBodyStyle(?string $body_style): self { $this->body_style = $body_style; return $this; }

    public function getDoors(): ?int { return $this->doors; }
    public function setDoors(?int $doors): self { $this->doors = $doors; return $this; }

    public function getSeats(): ?int { return $this->seats; }
    public function setSeats(?int $seats): self { $this->seats = $seats; return $this; }

    public function getListingDate(): \DateTimeInterface { return $this->listing_date; }
    public function setListingDate(\DateTimeInterface $date): self { $this->listing_date = $date; return $this; }

    public function hasSunroof(): bool { return $this->has_sunroof; }
    public function setHasSunroof(bool $flag): self { $this->has_sunroof = $flag; return $this; }

    public function hasNavigation(): bool { return $this->has_navigation; }
    public function setHasNavigation(bool $flag): self { $this->has_navigation = $flag; return $this; }

    public function hasHeatedSeats(): bool { return $this->has_heated_seats; }
    public function setHasHeatedSeats(bool $flag): self { $this->has_heated_seats = $flag; return $this; }

    public function getInteriorMaterial(): ?string { return $this->interior_material; }
    public function setInteriorMaterial(?string $material): self { $this->interior_material = $material; return $this; }
}
