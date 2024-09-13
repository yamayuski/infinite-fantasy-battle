<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Http\HttpHandler;

use JsonSerializable;

class OutputConverter
{
    /**
     * @param mixed $data
     * @return string
     */
    public function convert(mixed $data): string
    {
        if (\is_string($data)) {
            return $data;
        }

        if ($data instanceof JsonSerializable === false) {
            throw new InvalidHandlerDefinitionException('Output class must implement JsonSerializable');
        }
        $result = \json_encode($data, \JSON_UNESCAPED_UNICODE);

        if ($result === false) {
            throw new InvalidHandlerDefinitionException('Failed to encode JSON: ' . \json_last_error_msg());
        }

        return $result;
    }
}
