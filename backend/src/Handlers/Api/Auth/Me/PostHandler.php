<?php

declare(strict_types=1);

/**
 * @author Masaru Yamagishi <m.yamagishi90+git@gmail.com>
 * @license MIT
 */

namespace Ifb\Handlers\Api\Auth\Me;

use Ifb\Domain\Account\AccountEntity;
use Shibare\Log\Logger;

final readonly class PostHandler
{
    public function __construct(
    ) {}

    public function __invoke(PostInput $input): PostOutput
    {
        $request = $input->getServerRequest();
        if (\is_null($request)) {
            throw new \RuntimeException('ServerRequest is not set');
        }
        $account = $request->getAttribute(AccountEntity::class);
        if (\is_null($account) || !$account instanceof AccountEntity) {
            throw new \RuntimeException('Account is not set');
        }
        Logger::getInstance()->info('account', compact('account'));
        return new PostOutput($account->email);
    }
}
