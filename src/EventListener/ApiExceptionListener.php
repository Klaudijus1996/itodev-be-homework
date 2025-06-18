<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ApiExceptionListener
{
    public function __construct(private KernelInterface $kernel) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();
        $exception = $event->getThrowable();

        // 1) Only /api/*
        if (0 !== strpos($path, '/api/')) {
            return;
        }

        // 2) If it’s a validation failure, let the ValidationExceptionListener handle it:
        if ($exception->getPrevious() instanceof ValidationFailedException) {
            return;
        }

        // 3) If another listener already set a response, don’t override it
        if (!is_null($event->getResponse())) {
            return;
        }

        $statusCode = $exception->getStatusCode();

        // 4) Choose message (and trace) based on environment
        if ('prod' === $this->kernel->getEnvironment()) {
            $message = 'Service is currently not available, please try again later';
            $payload = [
                'message' => $message,
            ];
        } else {
            $payload = [
                'message' => $exception->getMessage() ?: JsonResponse::$statusTexts[$statusCode] ?? 'Error',
                'trace'   => $exception->getTraceAsString(),
            ];
        }

        $response = new JsonResponse($payload, $statusCode);

        // 5) Preserve any headers the exception might carry
        foreach ($exception->getHeaders() as $header => $values) {
            foreach ($values as $value) {
                $response->headers->set($header, $value, false);
            }
        }

        $event->setResponse($response);
    }
}
