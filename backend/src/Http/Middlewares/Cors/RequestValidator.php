<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\Middlewares\Cors;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
 * @package Ifb\Routing
 */
class RequestValidator
{
    public function __construct(
        private readonly CorsSetting $setting,
    ) {}

    /**
     * Validate request
     * @param ServerRequestInterface $request
     * @return RequestValidationResult
     */
    public function validate(ServerRequestInterface $request): RequestValidationResult
    {
        $origin_header = $this->getSingleHeader($request, 'Origin');
        if (\is_null($origin_header)) {
            return RequestValidationResult::ORIGIN_NOT_FOUND;
        }

        if ($this->isSameOrigin($origin_header)) {
            return RequestValidationResult::SAME_ORIGIN;
        }

        if (!$this->isAllowedOrigin($origin_header)) {
            return RequestValidationResult::ORIGIN_NOT_ALLOWED;
        }

        $request_method = $this->getSingleHeader($request, 'Access-Control-Request-Method');
        if (!$this->isAllowedMethod($request_method)) {
            return RequestValidationResult::METHOD_NOT_ALLOWED;
        }

        $request_headers = $request->getHeader('Access-Control-Request-Headers');
        if (\count($request_headers) > 0 && !$this->isAllowedHeadersAll($request_headers)) {
            return RequestValidationResult::HEADERS_NOT_ALLOWED;
        }

        return RequestValidationResult::VALID_CROSS_ORIGIN;
    }

    private function getSingleHeader(ServerRequestInterface $request, string $name): ?string
    {
        $headers = $request->getHeader($name);

        if (\count($headers) !== 1) {
            return null;
        }

        return $headers[0];
    }

    /**
     * Validate Origin header
     * @param string $origin
     * @return bool
     */
    private function isSameOrigin(string $origin): bool
    {
        $server_parts = \parse_url($this->setting->server_origin);

        if (!\is_array($server_parts)) {
            throw new InvalidArgumentException('Invalid server origin setting provided: ' . $this->setting->server_origin);
        }
        $origin_parts = \parse_url($origin);
        if (!\is_array($origin_parts)) {
            return false;
        }

        $is_same_scheme = 0 === \strcasecmp($server_parts['scheme'] ?? '', $origin_parts['scheme'] ?? '');
        $is_same_host = 0 === \strcasecmp($server_parts['host'] ?? '', $origin_parts['host'] ?? '');
        $is_same_port = ($server_parts['port'] ?? null) === ($origin_parts['port'] ?? null);

        return $is_same_scheme && $is_same_host && $is_same_port;
    }

    /**
     * Validate Origin header is allowed
     * @param string $origin
     * @return bool
     */
    private function isAllowedOrigin(string $origin): bool
    {
        foreach ($this->setting->allow_origin as $allow) {
            if (0 === \strcasecmp($origin, $allow)) {
                return true;
            }
        }
        return false;
    }

    private function isAllowedMethod(?string $method): bool
    {
        if (\is_null($method)) {
            return false;
        }

        foreach ($this->setting->allow_methods as $allow) {
            if (0 === \strcasecmp($method, $allow)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string[] $headers
     * @return bool
     */
    private function isAllowedHeadersAll(array $headers): bool
    {
        foreach ($headers as $header) {
            if (!$this->isAllowedHeader($header)) {
                return false;
            }
        }
        return true;
    }

    private function isAllowedHeader(string $header): bool
    {
        foreach ($this->setting->allow_headers as $allow) {
            if (0 === \strcasecmp($header, $allow)) {
                return true;
            }
        }
        return false;
    }
}
