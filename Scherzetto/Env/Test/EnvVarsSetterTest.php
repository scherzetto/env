<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benjamin
 * Date: 26/01/19
 * Time: 12:10.
 */

namespace Scherzetto\Env\Test;

use Scherzetto\Env\EnvVarsSetter;
use Scherzetto\Env\Parser\DotenvParser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EnvVarsSetterTest extends TestCase
{
    /** @var DotenvParser|MockObject */
    private $parser;

    /** @var EnvVarsSetter */
    private $setter;

    private $envPath = './tests/fixtures/.env';

    public function setUp()
    {
        $this->parser = $this->getMockBuilder(DotenvParser::class)
            ->setMethods(['parse'])
            ->getMock();
        $this->parser->/** @scrutinizer ignore-call */method('parse')->willReturn(['TEST_FOO' => 'foo']);
        $this->setter = new EnvVarsSetter($this->parser);
    }

    public function testEnvFileIsNotFoundWithWrongEnv()
    {
        $this->setter->loadEnv($this->envPath);
        $this->assertFalse(getenv('TEST_BAR'));
    }

    public function testEnvFileIsFoundWithEnv()
    {
        $this->setter->loadEnv($this->envPath, 'APP_ENV', 'test');
        $this->assertEquals('foo', getenv('TEST_FOO'));
    }
}
