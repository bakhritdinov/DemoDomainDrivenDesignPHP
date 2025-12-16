<?php

namespace App\Core\Domain\Language\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'language', options: [
    "comment" => "Языки"
])]
#[ORM\Entity]
class Language
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true, options: [
        "comment" => "Уникальный идентификатор"
    ])]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\Column(type: "string", length: 255, nullable: false, options: [
        "comment" => "Название"
    ])]
    private string $name;

    #[ORM\Column(type: "string", length: 255, unique: true, nullable: false, options: [
        "comment" => "Код"
    ])]
    private string $code;

    #[ORM\Column(type: "text", nullable: true, options: [
        "comment" => "Логотип"
    ])]
    private ?string $logo = null;

    #[ORM\Column(type: "boolean", options: [
        'default' => 1,
        "comment" => "Активный ли язык"
    ])]
    private bool $isActive = true;

    #[ORM\Column(type: "datetime", nullable: false, options: [
        "comment" => "Дата создания"
    ])]
    private \DateTime $createdAt;

    #[ORM\Column(type: "datetime", nullable: true, options: [
        "comment" => "Дата обновления"
    ])]
    private ?\DateTime $updatedAt = null;

    public function __construct(
        string  $name,
        string  $code,
        ?string $logo = null
    )
    {
        $this->name = $name;
        $this->code = $code;
        $this->logo = $logo;

        $this->createdAt = new \DateTime('now');
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function changeName(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = new \DateTime('now');
    }

    public function changeLogo(?string $logo): void
    {
        $this->logo = $logo;
        $this->updatedAt = new \DateTime('now');
    }

    public function changeIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
        $this->updatedAt = new \DateTime('now');
    }

    public function equalsIsActive(bool $isActive): bool
    {
        return $this->isActive === $isActive;
    }
}