<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <akai_inu@live.jp>
 * @license MIT
 */

namespace Ifb\Http\ValidatedRouter;

use Psr\Http\Message\ServerRequestInterface;

class ControllerRequestGenerator
{
    /**
     * Generate request object
     * @param ServerRequestInterface $server_request
     * @return ValidatedRequestInterface
     */
    public function generate(ServerRequestInterface $server_request): ValidatedRequestInterface
    {

    }
}
