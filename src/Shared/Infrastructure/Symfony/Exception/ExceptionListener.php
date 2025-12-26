<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Exception;

use App\Shared\Domain\Exception\DomainException;
use App\Shared\Domain\Exception\EntityNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Symfony\Http\ApiResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final class ExceptionListener
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        // Only handle API requests
        if (!str_starts_with($request->getPathInfo(), '/api/')) {
            return;
        }

        $this->logger->error('API Exception occurred', [
            'exception' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);

        $response = match (true) {
            $exception instanceof ValidationException => ApiResponse::validationError(
                $exception->getErrors()
            ),
            $exception instanceof ValidationFailedException => ApiResponse::validationError(
                $this->formatValidationErrors($exception)
            ),
            $exception instanceof EntityNotFoundException => ApiResponse::notFound(
                $exception->getMessage()
            ),
            $exception instanceof DomainException => ApiResponse::error(
                $exception->getMessage(),
                Response::HTTP_BAD_REQUEST
            ),
            $exception instanceof AuthenticationException => ApiResponse::unauthorized(
                $exception->getMessage()
            ),
            $exception instanceof AccessDeniedException => ApiResponse::forbidden(
                $exception->getMessage()
            ),
            $exception instanceof HttpExceptionInterface => ApiResponse::error(
                $exception->getMessage(),
                $exception->getStatusCode()
            ),
            default => ApiResponse::error(
                'Internal server error',
                Response::HTTP_INTERNAL_SERVER_ERROR
            )
        };

        $event->setResponse($response);
    }

    private function formatValidationErrors(ValidationFailedException $exception): array
    {
        $errors = [];
        foreach ($exception->getViolations() as $violation) {
            $errors[$violation->getPropertyPath()][] = $violation->getMessage();
        }
        return $errors;
    }
}