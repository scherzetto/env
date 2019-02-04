<?php

declare(strict_types=1);

namespace Scherzetto\Env\Exception;

use Throwable;

class EnvException extends \Exception
{
    // @codeCoverageIgnoreStart
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    // @codeCoverageIgnoreEnd
}
