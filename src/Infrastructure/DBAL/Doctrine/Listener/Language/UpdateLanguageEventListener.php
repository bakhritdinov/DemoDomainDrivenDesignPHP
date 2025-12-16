<?php

namespace App\Infrastructure\DBAL\Doctrine\Listener\Language;

use App\Core\Domain\Language\Entity\Language;
use App\Infrastructure\DBAL\Doctrine\Listener\Language\Normalizer\UpdateLanguageNormalizer;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: Language::class)]
class UpdateLanguageEventListener
{
    private const ROUTING_KEY = 'updated';

    public function __construct(
        public ContainerInterface       $container,
        public LoggerInterface          $logger,
        public UpdateLanguageNormalizer $updateLanguageNormalizer
    )
    {
    }

    public function postUpdate(Language $language): void
    {
        if (!$this->container->getParameter('sync.status')) {
            return;
        }

        if (!$this->container->getParameter('sync.entity.language')) {
            return;
        }

        $this
            ->container
            ->get('old_sound_rabbit_mq.update_language_producer')
            ->publish($this->updateLanguageNormalizer->normalize($language), self::ROUTING_KEY);

        $this->logger->info(sprintf('[Event] - Language with id %s updated and published', $language->getId()->toRfc4122()));
    }

}