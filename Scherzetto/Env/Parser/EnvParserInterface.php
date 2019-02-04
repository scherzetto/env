<?php

declare(strict_types=1);

namespace Scherzetto\Env\Parser;

interface EnvParserInterface
{
    public function parse(string $data): array;
}
