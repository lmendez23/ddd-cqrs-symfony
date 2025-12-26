<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final class ValidationMiddleware
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // Only apply validation to API requests
        if (!str_starts_with($request->getPathInfo(), '/api/')) {
            return;
        }

        // Sanitize request data to prevent injection attacks
        $this->sanitizeRequest($request);
    }

    private function sanitizeRequest(Request $request): void
    {
        // Sanitize query parameters
        foreach ($request->query->all() as $key => $value) {
            if (is_string($value)) {
                $request->query->set($key, $this->sanitizeString($value));
            }
        }

        // Sanitize request body for JSON requests
        if ($request->getContentType() === 'json') {
            $content = $request->getContent();
            if (!empty($content)) {
                $data = json_decode($content, true);
                if (is_array($data)) {
                    $sanitizedData = $this->sanitizeArray($data);
                    $request->initialize(
                        $request->query->all(),
                        $request->request->all(),
                        $request->attributes->all(),
                        $request->cookies->all(),
                        $request->files->all(),
                        $request->server->all(),
                        json_encode($sanitizedData)
                    );
                }
            }
        }
    }

    private function sanitizeArray(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = $this->sanitizeString($value);
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizeArray($value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        return $sanitized;
    }

    private function sanitizeString(string $value): string
    {
        // Remove potential XSS and SQL injection patterns
        $value = strip_tags($value);
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        
        // Remove common SQL injection patterns
        $patterns = [
            '/(\s|^)(union|select|insert|update|delete|drop|create|alter|exec|execute)(\s|$)/i',
            '/(\s|^)(script|javascript|vbscript|onload|onerror|onclick)/i',
        ];
        
        foreach ($patterns as $pattern) {
            $value = preg_replace($pattern, '', $value);
        }
        
        return trim($value);
    }
}