<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Handlers\Api\Auth\Me;

use Psr\Http\Message\ServerRequestInterface;

final class GetInput
{
    private ?ServerRequestInterface $serverRequest = null;

    public function setServerRequest(ServerRequestInterface $serverRequest): void
    {
        $this->serverRequest = $serverRequest;
    }

    public function getServerRequest(): ?ServerRequestInterface
    {
        return $this->serverRequest;
    }
}
