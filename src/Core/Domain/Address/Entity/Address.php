<?php

namespace App\Core\Domain\Address\Entity;

use App\Core\Domain\Address\ValueObject\Point;
use App\Core\Domain\City\Entity\City;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'address', options: [
    "comment" => "Адреса"
])]
#[ORM\Entity]
#[ORM\Index(columns: ["created_at"], name: "idx_address_created_at", options: ["where" => "(is_active = false)"])]
class Address
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true, options: [
        "comment" => "Уникальный идентификатор"
    ])]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: City::class)]
    #[ORM\JoinColumn(nullable: false, options: [
        "comment" => "Город"
    ])]
    private City $city;

    #[ORM\Column(type: "string", length: 500, unique: true, nullable: false, options: [
        "comment" => "Строка адреса"
    ])]
    private string $address;

    #[ORM\Column(type: "string", length: 50, nullable: true, options: [
        "comment" => "Почтовый индекс"
    ])]
    private ?string $postalCode;

    #[ORM\Column(type: "string", length: 100, nullable: false, options: [
        "comment" => "Улица"
    ])]
    private string $street;

    #[ORM\Column(type: "string", length: 50, nullable: false, options: [
        "comment" => "Дом"
    ])]
    private string $house;

    #[ORM\Column(type: "string", length: 50, nullable: true, options: [
        "comment" => "Квартира"
    ])]
    private ?string $flat;

    #[ORM\Column(type: "string", length: 50, nullable: true, options: [
        "comment" => "Подъезд"
    ])]
    private ?string $entrance;

    #[ORM\Column(type: "string", length: 50, nullable: true, options: [
        "comment" => "Этаж"
    ])]
    private ?string $floor;

    #[ORM\Column(type: "boolean", options: [
        'default' => 1,
        "comment" => "Активный ли адрес"
    ])]
    private bool $isActive = true;

    #[ORM\Column(type: "point", nullable: true, options: [
        "comment" => "Широта и Долгота"
    ])]
    private Point $point;

    #[ORM\Column(type: "datetime", nullable: false, options: [
        "comment" => "Дата создания"
    ])]
    private DateTime $createdAt;

    #[ORM\Column(type: "datetime", nullable: true, options: [
        "comment" => "Дата обновления"
    ])]
    private ?DateTime $updatedAt = null;

    public function __construct(
        City    $city,
        string  $address,
        string  $postalCode,
        string  $street,
        string  $house,
        ?string $flat,
        ?string $entrance,
        ?string $floor,
        ?Point  $point
    )
    {
        $this->city = $city;
        $this->address = $address;
        $this->postalCode = $postalCode;
        $this->street = $street;
        $this->house = $house;
        $this->flat = $flat;
        $this->entrance = $entrance;
        $this->floor = $floor;
        $this->point = $point;
        $this->createdAt = new DateTime('now');
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getHouse(): string
    {
        return $this->house;
    }

    public function getFlat(): ?string
    {
        return $this->flat;
    }

    public function getEntrance(): ?string
    {
        return $this->entrance;
    }

    public function getFloor(): ?string
    {
        return $this->floor;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getPoint(): Point
    {
        return $this->point;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function changeAddress(string $address): void
    {
        $this->address = $address;
        $this->updatedAt = new DateTime('now');
    }

    public function changePostalCode(string $postalCode): void
    {
        $this->postalCode = $postalCode;
        $this->updatedAt = new DateTime('now');
    }

    public function changeStreet(string $street): void
    {
        $this->street = $street;
        $this->updatedAt = new DateTime('now');
    }

    public function changeHouse(string $house): void
    {
        $this->house = $house;
        $this->updatedAt = new DateTime('now');
    }

    public function changeFlat(string $flat): void
    {
        $this->flat = $flat;
        $this->updatedAt = new DateTime('now');
    }

    public function changeEntrance(string $entrance): void
    {
        $this->entrance = $entrance;
        $this->updatedAt = new DateTime('now');
    }

    public function changeFloor(string $floor): void
    {
        $this->floor = $floor;
        $this->updatedAt = new DateTime('now');
    }

    public function changeIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
        $this->updatedAt = new DateTime('now');
    }

    public function equalsIsActive(bool $isActive): bool
    {
        return $this->isActive === $isActive;
    }
}