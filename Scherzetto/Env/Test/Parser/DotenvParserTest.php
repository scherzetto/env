<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benjamin
 * Date: 26/01/19
 * Time: 14:54.
 */

namespace Scherzetto\Env\Test\Parser;

use Scherzetto\Env\Parser\DotenvParser;
use PHPUnit\Framework\TestCase;

class DotenvParserTest extends TestCase
{
    /** @var string */
    private $data;

    /** @var DotenvParser */
    private $parser;

    public function setUp(): void
    {
        $this->data = file_get_contents('./Scherzetto/Env/Test/fixtures/.env.test');
        $this->parser = new DotenvParser();
    }

    public function testDataIsParsed()
    {
        $result = $this->parser->parse($this->data);
        $this->assertIsArray($result);
    }

    /**
     * @dataProvider envVarsProvider
     */
    public function testValuesAreCorrect($name, $value)
    {
        $result = $this->parser->parse($this->data);
        $this->assertEquals($result[$name], $value);
    }

    public function envVarsProvider()
    {
        return [
            ['TEST_UNQUOTED_VALUE', 'foo'],
            ['TEST_QUOTED_VALUE', 'This value contains spaces'],
            ['TEST_UNQUOTED_SPECIAL_CHAR', '$fjhs|%::saqzrfgs#foo'],
            ['TEST_QUOTED_SPECIAL_CHARS', 'dg$ag,k@zrg%|qwdf#qsf'],
            ['TEST_STRIPPED_COMMENT', 'foo'],
        ];
    }
}
