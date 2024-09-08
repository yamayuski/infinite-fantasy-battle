<?php

declare(strict_types=1);

/**
 * @license MIT
 */

namespace Ifb\Http\Cors;

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

    public function validate(ServerRequestInterface $request): RequestValidationResult
    {
        $origin_header = $this->getSingleHeader($request, 'Origin');
        if (\is_null($origin_header)) {
            return RequestValidationResult::ORIGIN_NOT_FOUND;
        }

        if ($this->isSameOrigin($origin_header)) {
            return RequestValidationResult::SAME_ORIGIN;
        }

        if (!$this->validateOrigin($origin_header)) {
            return RequestValidationResult::ORIGIN_NOT_ALLOWED;
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
     * Validate Host header
     * @param string $host
     * @return bool
     * @throws InvalidArgumentException
     */
    private function isSameHost(string $host): bool
    {
        $server_host_part = \parse_url($this->setting->server_origin, \PHP_URL_HOST);
        if (!\is_string($server_host_part)) {
            throw new \InvalidArgumentException('Invalid server host setting provided');
        }
        $server_port_part = \parse_url($this->setting->server_origin, \PHP_URL_PORT);
        if (!\is_string($server_port_part) && !\is_null($server_port_part)) {
            throw new \InvalidArgumentException('Invalid server port setting provided');
        }

        $host_part = \parse_url($host, \PHP_URL_HOST);
        if (!\is_string($host_part)) {
            return false;
        }
        $port_part = \parse_url($host, \PHP_URL_PORT);
        \assert(\is_int($port_part) || \is_null($port_part));

        $is_same_host = 0 === \strcasecmp($server_host_part, $host_part);
        $is_same_port = $server_port_part === $port_part;

        return $is_same_host && $is_same_port;
    }

    /**
     * Validate Origin header
     * @param string $origin
     * @return bool
     */
    private function isSameOrigin(string $origin): bool
    {
        if (!$this->isSameHost($origin)) {
            return false;
        }

        $server_scheme_part = \parse_url($this->setting->server_origin, \PHP_URL_SCHEME);

        $scheme_part = \parse_url($origin, \PHP_URL_SCHEME);
        if (!\is_string($scheme_part)) {
            return false; // @codeCoverageIgnore
        }

        return 0 === \strcasecmp($scheme_part, $server_scheme_part);
    }

    private function validateOrigin(string $origin): bool
    {
        foreach ($this->setting->allow_origin as $allow) {
            if (0 === \strcasecmp($origin, $allow)) {
                return true;
            }
        }
        return false;
    }
}
