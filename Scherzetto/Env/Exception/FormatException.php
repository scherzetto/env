<?php

declare(strict_types=1);

namespace Scherzetto\Env\Exception;

class FormatException extends EnvException
{
    // @codeCoverageIgnoreStart
    public function __construct(string $property = '', string $actual = '')
    {
        parent::__construct(sprintf('Wrong character in env var "%s", "%s" given', $property, $actual));
    }

    // @codeCoverageIgnoreEnd
}
