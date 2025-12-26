<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Http;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class RateLimitMiddleware
{
    private const RATE_LIMIT = 100; // requests per minute
    private const WINDOW_SIZE = 60; // seconds

    public function __construct(
        private readonly CacheItemPoolInterface $cache,
        private readonly TokenStorageInterface $tokenStorage
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // Only apply rate limiting to API requests
        if (!str_starts_with($request->getPathInfo(), '/api/')) {
            return;
        }

        // Skip rate limiting for unauthenticated requests (they have their own limits)
        $token = $this->tokenStorage->getToken();
        if (!$token || !$token->getUser()) {
            return;
        }

        $userId = $token->getUserIdentifier();
        $key = sprintf('rate_limit_%s', md5($userId));
        
        $cacheItem = $this->cache->getItem($key);
        $requests = $cacheItem->isHit() ? $cacheItem->get() : [];
        
        $now = time();
        $windowStart = $now - self::WINDOW_SIZE;
        
        // Remove old requests outside the window
        $requests = array_filter($requests, fn($timestamp) => $timestamp > $windowStart);
        
        if (count($requests) >= self::RATE_LIMIT) {
            $response = ApiResponse::error(
                'Rate limit exceeded. Try again later.',
                Response::HTTP_TOO_MANY_REQUESTS
            );
            $response->headers->set('Retry-After', (string) self::WINDOW_SIZE);
            $event->setResponse($response);
            return;
        }
        
        // Add current request
        $requests[] = $now;
        
        $cacheItem->set($requests);
        $cacheItem->expiresAfter(self::WINDOW_SIZE);
        $this->cache->save($cacheItem);
    }
}