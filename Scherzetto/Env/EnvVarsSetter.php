<?php

declare(strict_types=1);

namespace Scherzetto\Env;

use Scherzetto\Env\Exception\EnvException;
use Scherzetto\Env\Exception\PathException;
use Scherzetto\Env\Parser\EnvParserInterface;

final class EnvVarsSetter
{
    public const ENV_DEV = 'dev';
    public const ENV_TEST = 'test';
    public const ENV_PROD = 'prod';
    private const DIST_EXT = '.dist';

    /** @var array */
    private $envVars;

    /** @var EnvParserInterface */
    private $parser;

    public function __construct(EnvParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param  string       $path
     * @param  string       $envVarName
     * @param  string       $defaultEnv
     * @param  array        $testEnvs
     * @throws EnvException
     */
    public function loadEnv(string $path, string $envVarName = 'APP_ENV', string $defaultEnv = self::ENV_DEV, $testEnvs = [self::ENV_TEST]): void
    {
        $env = $this->checkAppEnv($envVarName, $defaultEnv);

        $this->loadDistOrNot($path);

        if (!\in_array($env, $testEnvs, true) && file_exists($file = "$path.local")) {
            $this->doLoad($file);
        }
        foreach (["$path.$env", "$path.$env.local"] as $file) {
            if (file_exists($file)) {
                $this->doLoad($file);
            }
        }
    }

    /**
     * @param  string       $path
     * @throws EnvException
     */
    public function doLoad(string $path)
    {
        if (!is_readable($path) || is_dir($path)) {
            throw new PathException();
        }
        try {
            $this->populate($path);
        } catch (EnvException $e) {
            throw $e;
        }
    }

    private function checkAppEnv(string $envVarName, string $defaultEnv): string
    {
        if (null === $env = $_SERVER[$envVarName] ?? $_ENV[$envVarName] ?? null) {
            $this->envVars[$envVarName] = $env = $defaultEnv;
        }

        return $env;
    }

    private function loadDistOrNot(string $path): void
    {
        $file = $path.self::DIST_EXT;
        if (file_exists($path) && !file_exists($file)) {
            $this->doLoad($path);
        } elseif (file_exists($file)) {
            $this->doLoad($file);
        }
    }

    /**
     * @param  string       $path
     * @throws EnvException
     */
    private function populate(string $path): void
    {
        $vars = $this->parser->parse(file_get_contents($path));
        $vars['APP_ROOT'] = getcwd();

        foreach ($vars as $varName => $value) {
            $httpVar = 0 !== mb_strpos($varName, 'HTTP_');

            if (isset($_ENV[$varName]) || ($httpVar && isset($_SERVER[$varName]))) {
                continue;
            }

            putenv("$varName=$value");
            $this->envVars[$varName] = $_ENV[$varName] = $value;
            if ($httpVar) {
                $_SERVER[$varName] = $value;
            }
        }
    }
}
