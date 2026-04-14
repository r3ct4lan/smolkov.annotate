<?php

namespace Smolkov\Annotate;

use ReflectionException;
use ReflectionMethod;
use Smolkov\Annotate\Exception\AnnotationException;
use Smolkov\Annotate\Helper\Output;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Executor
{
    /**
     * @noinspection PhpUndefinedClassInspection
     * @noinspection PhpUndefinedNamespaceInspection
     */
    private const COMMAND_CLASSES = [
        \Bitrix\Main\Cli\Command\Orm\AnnotateCommand::class,
        \Bitrix\Main\Cli\OrmAnnotateCommand::class,
    ];

    private InputInterface $input;

    public function __construct(string $modules, ?string $output = null, bool $clean = false)
    {
        $output = (new Output($output))->setModuleName($modules)->getValue();

        $this->input = new StringInput(
            sprintf(
                '--modules=%s %s %s',
                escapeshellarg($modules),
                $clean ? '--clean' : '',
                escapeshellarg($output),
            )
        );
    }

    /**
     * @return string
     * @throws AnnotationException
     * @throws ExceptionInterface
     * @throws ReflectionException
     */
    public function run(): string
    {
        $cliOutput = new BufferedOutput();
        $command = $this->createCommand();

        $command->run($this->input, $cliOutput);

        return $cliOutput->fetch();
    }

    /**
     * @throws ReflectionException
     * @throws AnnotationException
     */
    private function createCommand(): Command
    {
        foreach (self::COMMAND_CLASSES as $commandClass) {
            if (class_exists($commandClass)) {
                $command = new $commandClass();

                return $this->ensureCommandReturnsExitCode($command);
            }
        }

        throw new AnnotationException('ORM annotate command is not found.');
    }

    /**
     * @throws ReflectionException
     */
    private function ensureCommandReturnsExitCode(Command $command): Command
    {
        $execute = new ReflectionMethod($command, 'execute');
        if (PHP_VERSION_ID < 80100) {
            /** @noinspection PhpExpressionResultUnusedInspection */
            $execute->setAccessible(true);
        }

        if ($execute->hasReturnType()) {
            return $command;
        }

        $command->setCode(
            static function (InputInterface $input, OutputInterface $output) use ($command, $execute): int {
                $result = $execute->invoke($command, $input, $output);

                return is_numeric($result) ? (int)$result : 0;
            }
        );

        return $command;
    }
}
