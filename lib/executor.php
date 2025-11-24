<?php

namespace Orm\Annotate;

use Bitrix\Main\Cli\Command\Orm\AnnotateCommand;
use Orm\Annotate\Helpers\Output;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class Executor
{
    private StringInput $input;

    public function __construct(string $modules, ?string $output = null, bool $clean = false)
    {
        $output = (new Output($output))->setModuleName($modules)->getValue();

        $this->input = new StringInput(sprintf(
            '--modules=%s %s %s',
            escapeshellarg($modules),
            $clean ? '--clean' : '',
            escapeshellarg($output),
        ));

    }

    /**
     * @throws ExceptionInterface
     */
    public function run(): string
    {
        $cliOutput = new BufferedOutput();

        (new AnnotateCommand())->run($this->input, $cliOutput);

        return $cliOutput->fetch();
    }
}