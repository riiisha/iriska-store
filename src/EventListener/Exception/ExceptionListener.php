<?php

namespace App\EventListener\Exception;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

#[AsEventListener]
readonly class ExceptionListener
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

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
        }
    }
}
