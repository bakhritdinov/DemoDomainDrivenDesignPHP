<?php

namespace App\Core\Domain\Currency\Entity;

use App\Core\Domain\Currency\Repository\CurrencyRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'currency', options: [
    "comment" => "Валюта"
])]
#[ORM\Entity(repositoryClass: CurrencyRepositoryInterface::class)]
#[ORM\Index(columns: ["name"], name: "idx_currency_name", options: ["where" => "(is_active = true)"])]
#[ORM\Index(columns: ["created_at"], name: "idx_currency_created_at", options: ["where" => "(is_active = true)"])]
class Currency
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true, options: [
        "comment" => "Уникальный идентификатор"
    ])]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\Column(type: "string", length: 3, unique: true, nullable: false, options: [
        "comment" => "Уникальный код валюты"
    ])]
    private string $code;

    #[ORM\Column(type: "integer", length: 3, unique: true, nullable: false, options: [
        "comment" => "Уникальный номер валюты"
    ])]
    private int $num;

    #[ORM\Column(type: "string", length: 100, nullable: false, options: [
        "comment" => "Название"
    ])]
    private string $name;

    #[ORM\OneToMany(mappedBy: 'currencyFrom', targetEntity: CurrencyRate::class, cascade: ['persist'])]
    private Collection $currencyRates;

    #[ORM\Column(type: "boolean", options: [
        'default' => 1,
        "comment" => "Активная ли валюта"]
    )]
    private bool $isActive = true;

    #[ORM\Column(type: "datetime", nullable: false, options: [
        "comment" => "Дата создания"
    ])]
    private \DateTime $createdAt;

    #[ORM\Column(type: "datetime", nullable: true, options: [
        "comment" => "Дата обновления"
    ])]
    private ?\DateTime $updatedAt = null;

    public function __construct(string $code, int $num, string $name)
    {
        $this->code = $code;
        $this->num = $num;
        $this->name = $name;

        $this->currencyRates = new ArrayCollection();

        $this->createdAt = new \DateTime('now');
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getNum(): int
    {
        return $this->num;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCurrencyRates(): Collection
    {
        return $this->currencyRates
            ->matching(Criteria::create()->where(Criteria::expr()->eq("expiredAt", null)));
    }

    public function getCurrencyRateByCurrencyToCode(Currency $currencyTo): ?CurrencyRate
    {
        return $this->currencyRates
            ->matching(Criteria::create()
                ->where(Criteria::expr()->eq("expiredAt", null))
                ->andWhere(Criteria::expr()->eq("currencyTo", $currencyTo->getId()))
            )->first();
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
