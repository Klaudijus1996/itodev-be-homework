<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ValidationExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // MapRequestPayload wraps ValidationFailedException in an HttpException
        if (!$exception instanceof HttpException) {
            return;
        }

        $prev = $exception->getPrevious();
        if (!$prev instanceof ValidationFailedException) {
            return;
        }

        // Build the errors array: [ field => [messages...] ]
        $errors = [];
        foreach ($prev->getViolations() as $violation) {
            $field = $violation->getPropertyPath();
            $errors[$field][] = $violation->getMessage();
        }

        $response = new JsonResponse([
            'message' => 'Validation Failed',
            'errors'  => $errors,
        ], Response::HTTP_UNPROCESSABLE_ENTITY);

        $event->setResponse($response);
    }
}
