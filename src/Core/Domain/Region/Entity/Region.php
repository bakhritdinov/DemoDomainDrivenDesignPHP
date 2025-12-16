<?php

namespace App\Core\Domain\Region\Entity;

use App\Core\Domain\Country\Entity\Country;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'region', options: [
    "comment" => "Регионы"
])]
#[ORM\Entity]
#[ORM\Index(columns: ["created_at"], name: "idx_region_created_at", options: ["where" => "(is_active = false)"])]
class Region
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true, options: [
        "comment" => "Уникальный идентификатор"
    ])]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(nullable: false, options: [
        "comment" => "Страна"
    ])]
    private Country $country;

    #[ORM\Column(type: "string", length: 255, unique: true, nullable: false, options: [
        "comment" => "Название региона"
    ])]
    private string $name;

    #[ORM\Column(type: "string", length: 7, unique: true, nullable: false, options: [
        "comment" => "Код региона"
    ])]
    private string $code;

    #[ORM\Column(type: "boolean", options: [
        'default' => 1,
        "comment" => "Активный ли регион"
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

    public function __construct(Country $country, string $name, string $code)
    {
        $this->country = $country;
        $this->name = $name;
        $this->code = $code;
        $this->createdAt = new DateTime('now');
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
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

    public function changeCountry(Country $country): void
    {
        $this->country = $country;
        $this->updatedAt = new DateTime('now');
    }

    public function changeName(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = new DateTime('now');
    }

    public function changeCode(string $code): void
    {
        $this->code = $code;
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
