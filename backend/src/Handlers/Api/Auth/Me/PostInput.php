<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Handlers\Api\Auth\Me;

use Shibare\Contracts\HttpServer\ServerRequestAwareInterface;
use Shibare\HttpServer\HttpHandler\ServerRequestAwareTrait;

final class PostInput implements ServerRequestAwareInterface
{
    use ServerRequestAwareTrait;
}
