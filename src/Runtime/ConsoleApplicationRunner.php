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

        while(true) {
            $lambda->processNextEvent(function ($event, Context $context): array {
                error_log(var_dump($event));

                $input = new ArgvInput(explode(' ', (string) $event));
                $output = new BufferedOutput();
                $exitCode = $this->application->run($input, $output);

                // Echo the output so that it is written to CloudWatch logs
                echo $output->fetch();

                if ($exitCode > 0) {
                    throw new \RuntimeException('The command exited with a non-zero status code: ' . $exitCode);
                }

                return [
                    'exitCode' => $exitCode, // will always be 0
                    'output' => $output->fetch(),
                ];
            });
        }
    }
}
