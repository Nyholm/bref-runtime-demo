<?php

namespace App\Runtime;

use Bref\Context\Context;
use Bref\Runtime\LambdaRuntime;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Runtime\RunnerInterface;

class ConsoleApplicationRunner implements RunnerInterface
{
    private $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function run(): int
    {
        $lambda = LambdaRuntime::fromEnvironmentVariable();

        $lambda->processNextEvent(function ($event, Context $context): array {
            error_log(var_dump($event));
            if (is_array($event)) {
                // Backward compatibility with the former CLI invocation format
                $cliOptions = $event['cli'] ?? '';
            } elseif (is_string($event)) {
                $cliOptions = $event;
            } else {
                $cliOptions = '';
            }

            $input = new ArgvInput(explode(' ', $cliOptions));
            $output = new BufferedOutput();
            $exitCode = $this->application->run($input, $output);

            // Echo the output so that it is written to CloudWatch logs
            echo $output->fetch();

            if ($exitCode > 0) {
                throw new \RuntimeException('The command exited with a non-zero status code: '.$exitCode);
            }

            return [
                'exitCode' => $exitCode, // will always be 0
                'output' => $output->fetch(),
            ];
        });

        return 0;
    }
}
