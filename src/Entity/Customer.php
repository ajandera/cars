<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CustomerRepository;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\Table(name: "customer")]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 32)]
    private string $car_hex_id;

    #[ORM\Column(type: "string", length: 50)]
    private string $first_name;

    #[ORM\Column(type: "string", length: 50)]
    private string $last_name;

    #[ORM\Column(type: "string", length: 100)]
    private string $email;

    #[ORM\Column(type: "string", length: 25, nullable: true)]
    private ?string $phone_number = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $address_street = null;

    #[ORM\Column(type: "string", length: 100, nullable: true)]
    private ?string $address_city = null;

    #[ORM\Column(type: "string", length: 100, nullable: true)]
    private ?string $address_state = null;

    #[ORM\Column(type: "string", length: 20, nullable: true)]
    private ?string $address_postal_code = null;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private ?string $address_country = null;

    #[ORM\Column(type: "date", nullable: true)]
    private ?\DateTimeInterface $date_of_birth = null;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private \DateTimeInterface $registration_date;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $last_login = null;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $newsletter_subscribed = false;

    #[ORM\Column(type: "string", length: 20, nullable: true)]
    private ?string $account_status = null;

    // --- Getters/Setters ---
    public function getId(): ?int { return $this->id; }

    public function getCarHexId(): string { return $this->car_hex_id; }
    public function setCarHexId(string $hex): self { $this->car_hex_id = $hex; return $this; }

    public function getFirstName(): string { return $this->first_name; }
    public function setFirstName(string $v): self { $this->first_name = $v; return $this; }

    public function getLastName(): string { return $this->last_name; }
    public function setLastName(string $v): self { $this->last_name = $v; return $this; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $v): self { $this->email = $v; return $this; }

    public function getPhoneNumber(): ?string { return $this->phone_number; }
    public function setPhoneNumber(?string $v): self { $this->phone_number = $v; return $this; }

    public function getAddressStreet(): ?string
    {
        return $this->address_street;
    }

    public function setAddressStreet(?string $street): self
    {
        $this->address_street = $street;
        return $this;
    }

    public function getAddressCity(): ?string
    {
        return $this->address_city;
    }

    public function setAddressCity(?string $city): self
    {
        $this->address_city = $city;
        return $this;
    }

    public function getAddressState(): ?string
    {
        return $this->address_state;
    }

    public function setAddressState(?string $state): self
    {
        $this->address_state = $state;
        return $this;
    }

    public function getAddressPostalCode(): ?string
    {
        return $this->address_postal_code;
    }

    public function setAddressPostalCode(?string $postalCode): self
    {
        $this->address_postal_code = $postalCode;
        return $this;
    }

    public function getAddressCountry(): ?string { return $this->address_country; }
    public function setAddressCountry(?string $v): self { $this->address_country = $v; return $this; }

    public function getFullName(): string { return trim($this->first_name . ' ' . $this->last_name); }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->date_of_birth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dob): self
    {
        $this->date_of_birth = $dob;
        return $this;
    }

    public function getRegistrationDate(): \DateTimeInterface
    {
        return $this->registration_date;
    }

    public function setRegistrationDate(\DateTimeInterface $date): self
    {
        $this->registration_date = $date;
        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->last_login;
    }

    public function setLastLogin(?\DateTimeInterface $login): self
    {
        $this->last_login = $login;
        return $this;
    }

    public function getAccountStatus(): string
    {
        return $this->account_status;
    }

    public function setAccountStatus(string $status): self
    {
        $this->account_status = $status;
        return $this;
    }

    public function isNewsletterSubscribed(): bool
    {
        return $this->newsletter_subscribed;
    }

    public function setNewsletterSubscribed(bool $subscribed): self
    {
        $this->newsletter_subscribed = $subscribed;
        return $this;
    }
}
