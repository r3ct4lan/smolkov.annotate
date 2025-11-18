<?php

namespace Orm\Annotate;

use Bitrix\Main\Cli\Command\Orm\AnnotateCommand;
use Orm\Annotate\Helpers\Output;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class Executor
{
    public static function run(string $modules, ?string $output = null, bool $clean = false): string
    {
        $output = (new Output($output))->getValue();

        $strInput = sprintf(
            '--modules=%s %s %s',
            escapeshellarg($modules),
            $clean ? '--clean' : '',
            escapeshellarg($output),
        );

        $cliCommand = new AnnotateCommand();
        $cliInput = new StringInput($strInput);
        $cliOutput = new BufferedOutput();

        $cliCommand->run($cliInput, $cliOutput);

        return $cliOutput->fetch();
    }
}