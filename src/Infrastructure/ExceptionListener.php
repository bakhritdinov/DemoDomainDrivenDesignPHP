<?php

namespace App\Infrastructure;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;

readonly class ExceptionListener implements EventSubscriberInterface
{
    public function __construct(public LoggerInterface $logger)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 200],
        ];
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        $context = [
            'code' => $this->getCode($exception),
            'message' => $exception->getMessage(),
            'trace' => $exception->getTrace(),
        ];

        $event->setResponse(
            new JsonResponse($context, $context['code'])
        );

        $this->logger->critical('Critical Exception', $context);
    }

    protected function getCode(\Throwable $exception): int
    {
        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getCode();
        }

        return $exception->getCode() ?: 500;
    }
}