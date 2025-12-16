<?php

namespace App\Core\Domain\Currency\Entity;

use App\Core\Domain\Currency\Repository\CurrencyRepositoryInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'currency_rate', options: [
    "comment" => "Конвертация валюты"
])]
#[ORM\Entity(repositoryClass: CurrencyRepositoryInterface::class)]
#[ORM\Index(columns: ["currencyFrom", "currencyTo"], name: "idx_currency_rate_from_to", options: ["where" => "(expired_at is null)"])]
#[ORM\Index(columns: ["created_at"], name: "idx_currency_rate_created_at", options: ["where" => "(expired_at is null)"])]
class CurrencyRate
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true, options: [
        "comment" => "Уникальный идентификатор"
    ])]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Currency::class, cascade: ['persist'], inversedBy: 'currencyRates')]
    #[ORM\JoinColumn(name: 'currencyFrom', nullable: false, options: [
        "comment" => "Из валюты"
    ])]
    private Currency $currencyFrom;

    #[ORM\ManyToOne(targetEntity: Currency::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'currencyTo', nullable: false, options: [
        "comment" => "В валюту"
    ])]
    private Currency $currencyTo;

    #[ORM\Column(type: "float", nullable: false, options: [
        "comment" => "Ставка"
    ])]
    private float $rate;

    #[ORM\Column(type: "datetime", nullable: false, options: [
        "comment" => "Дата создания"
    ])]
    private \DateTime $createdAt;

    #[ORM\Column(type: "datetime", nullable: true, options: [
        "comment" => "Дата окончания действия записи"
    ])]
    private ?\DateTime $expiredAt = null;

    public function __construct(Currency $currencyFrom, Currency $currencyTo, float $rate)
    {
        $this->currencyFrom = $currencyFrom;
        $this->currencyTo = $currencyTo;
        $this->rate = $rate;

        $this->createdAt = new \DateTime('now');
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCurrencyFrom(): Currency
    {
        return $this->currencyFrom;
    }

    public function getCurrencyTo(): Currency
    {
        return $this->currencyTo;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getExpiredAt(): ?\DateTime
    {
        return $this->expiredAt;
    }

    public function expired(): void
    {
        $this->expiredAt = new \DateTime('now');
    }
}
