<?php

namespace App\Core\Domain\City\Entity;

use App\Core\Domain\Region\Entity\Region;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'city', options: [
    "comment" => "Города"
])]
#[ORM\UniqueConstraint(
    name: 'city_unique_region_id_type_name',
    columns: ['region_id', 'type', 'name']
)]
#[ORM\Entity]
#[ORM\Index(columns: ["created_at"], name: "idx_city_created_at", options: ["where" => "(is_active = false)"])]
class City
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true, options: [
        "comment" => "Уникальный идентификатор"
    ])]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Region::class)]
    #[ORM\JoinColumn(nullable: false, options: [
        "comment" => "Регион"
    ])]
    private Region $region;

    #[ORM\Column(type: "string", length: 255, nullable: false, options: [
        "comment" => "Название города"
    ])]
    private string $name;

    #[ORM\Column(type: "string", length: 50, nullable: false, options: [
        "comment" => "Тип города"
    ])]
    private string $type;

    #[ORM\Column(type: "boolean", options: [
        'default' => 1,
        "comment" => "Активный ли город"
    ])]
    private bool $isActive = true;

    #[ORM\Column(type: "datetime", nullable: false, options: [
        "comment" => "Дата создания"
    ])]
    private DateTime $createdAt;

    #[ORM\Column(type: "datetime", nullable: true, options: [
        "comment" => "Дата обновления"
    ])]
    private ?DateTime $updatedAt = null;

    public function __construct(Region $region, string $type, string $name)
    {
        $this->region = $region;
        $this->type = $type;
        $this->name = $name;
        $this->createdAt = new DateTime('now');
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getRegion(): Region
    {
        return $this->region;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function changeType(string $type): void
    {
        $this->type = $type;
        $this->updatedAt = new DateTime('now');
    }

    public function changeRegion(Region $region): void
    {
        $this->region = $region;
        $this->updatedAt = new DateTime('now');
    }

    public function changeName(string $name): void
    {
        $this->name = $name;
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