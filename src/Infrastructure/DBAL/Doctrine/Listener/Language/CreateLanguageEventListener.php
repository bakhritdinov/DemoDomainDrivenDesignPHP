<?php

namespace App\Infrastructure\DBAL\Doctrine\Listener\Language;

use App\Core\Domain\Language\Entity\Language;
use App\Infrastructure\DBAL\Doctrine\Listener\Language\Normalizer\CreateLanguageNormalizer;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Language::class)]
class CreateLanguageEventListener
{
    private const ROUTING_KEY = 'created';

    public function __construct(
        public ContainerInterface       $container,
        public LoggerInterface          $logger,
        public CreateLanguageNormalizer $createLanguageNormalizer
    )
    {
    }

    public function postPersist(Language $language): void
    {
        if (!$this->container->getParameter('sync.status')) {
            return;
        }

        if (!$this->container->getParameter('sync.entity.language')) {
            return;
        }

        $this
            ->container
            ->get('old_sound_rabbit_mq.create_language_producer')
            ->publish($this->createLanguageNormalizer->normalize($language), self::ROUTING_KEY);

        $this->logger->info(sprintf('[Event] - Language with id %s created and published', $language->getId()->toRfc4122()));
    }
}