<?php

namespace App\EventListener\Exception;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

#[AsEventListener]
readonly class ExceptionListener
{
    public function __construct(
        private LoggerInterface     $logger,
        private ?ContainerInterface $container = null
    ) {
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($this->isDebug()) {
            throw $exception;
        }

        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = 'An error occurred.';

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage();
        }

        $responseContent = [
            'error' => [
                'code' => $statusCode,
                'message' => $message
            ],
        ];

        $response = new JsonResponse($responseContent, $statusCode);
        $event->setResponse($response);

        if ($statusCode >= 500) {
            $this->logger->error('An error occurred', ['exception' => $exception]);
        } elseif ($statusCode >= 400 and $statusCode !== Response::HTTP_NOT_FOUND) {
            $this->logger->debug('An error occurred', ['exception' => $exception]);
        }
    }

    private function isDebug()
    {
        try {
            return $this->container->get('kernel')->isDebug();
        } catch (Throwable) {
            return false;
        }
    }
}
