<?php

declare(strict_types=1);

namespace Scherzetto\Env\Parser;

use Scherzetto\Env\Exception\EnvException;
use Scherzetto\Env\Exception\FormatException;

class DotenvParser implements EnvParserInterface
{
    private const REGEX_VARNAME = '/(export[ \t]++)?((?i:[A-Z][A-Z0-9_]*+))/A';
    private const REGEX_QUOTED = '/["\']+(?:.*)["\']+$/A';
    private const REGEX_EMPTY_OR_COMMENT = '/(?:\s*+(?:#[^\n]*+)?+)++/A';

    /**
     * @param  string       $data
     * @throws EnvException
     * @return array
     */
    public function parse(string $data): array
    {
        $values = [];
        $lines = explode("\n", $data);
        foreach ($lines as $line) {
            if ($this->emptyLine($line)) {
                continue;
            }
            try {
                [$name, $value] = explode('=', $line, 2);
                $name = $this->lexName($name);
                $value = $this->lexValue($value);
            } catch (EnvException $e) {
                throw $e;
            }
            $values[$name] = $value;
        }

        return $values;
    }

    private function emptyLine(string $line): bool
    {
        preg_match(self::REGEX_EMPTY_OR_COMMENT, $line, $matches, 0);

        return $matches[0] !== '' || $line === '';
    }

    /**
     * @param  string          $name
     * @throws FormatException
     * @return string
     */
    private function lexName(string $name): string
    {
        if (!preg_match(self::REGEX_VARNAME, $name, $matches)) {
            throw new FormatException('name', $name);
        }

        return $matches[2];
    }

    /**
     * @param string $value
     *
     * @throws FormatException
     *
     * @return string
     */
    private function lexValue(string $value): string
    {
        // strip inline comments on the right hand side
        $value = explode(' #', $value, 2)[0];

        if ($this->isQuoted($value)) {
            // strip quotes
            $value = mb_substr(mb_substr($value, 0, -1), 1);

            $pos = mb_strpos($value, '"');
            if (
                false !== $pos &&
                mb_strlen($value) - 1 !== $pos &&
                $value[$pos - 1] !== '\\'
            ) {
                throw new FormatException('value', $value);
            }
        }

        return $value;
    }

    private function isQuoted(string $value): bool
    {
        return (bool) preg_match(self::REGEX_QUOTED, $value);
    }
}
