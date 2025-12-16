<?php

namespace App\Core\Domain\Country\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'country', options: [
    "comment" => "Страны"
])]
#[ORM\Entity]
#[ORM\Index(columns: ["created_at"], name: "idx_country_created_at", options: ["where" => "(is_active = false)"])]
class Country
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true, options: [
        "comment" => "Уникальный идентификатор"
    ])]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\Column(type: "string", length: 250, unique: true, nullable: false, options: [
        "comment" => "Название страны"
    ])]
    private string $name;

    #[ORM\Column(type: "smallint", unique: true, nullable: false, options: [
        "comment" => "Код страны",
        "unsigned" => true
    ])]
    private int $numericCode;

    #[ORM\Column(type: "string", length: 2, unique: true, nullable: false, options: [
        "comment" => "Код Alpha2 страны"
    ])]
    private string $alpha2;

    #[ORM\Column(type: "string", length: 3, unique: true, nullable: false, options: [
        "comment" => "Код Alpha3 страны"
    ])]
    private string $alpha3;

    #[ORM\Column(type: "boolean", options: [
        'default' => 1,
        "comment" => "Активная ли страна"
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

    public function __construct(string $name, int $numericCode, string $alpha2, string $alpha3)
    {
        $this->name = $name;
        $this->numericCode = $numericCode;
        $this->alpha2 = $alpha2;
        $this->alpha3 = $alpha3;
        $this->createdAt = new DateTime('now');
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNumericCode(): int
    {
        return $this->numericCode;
    }

    public function getAlpha2(): string
    {
        return $this->alpha2;
    }

    public function getAlpha3(): string
    {
        return $this->alpha3;
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

    public function toString(): string
    {
        return $this->name;
    }
}