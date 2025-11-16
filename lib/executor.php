<?php

namespace Orm\Annotate;

use Bitrix\Main\Cli\Command\Orm\AnnotateCommand;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class Executor
{
    public static function run(string $modules, ?string $output = null, bool $clean = false): bool
    {
        $output = is_null($output)
            ? 'orm.annotate.php'
            : $output;

        $cliCommand = new AnnotateCommand();
        $cliInput = new StringInput(sprintf(
            '--modules=%s %s %s',
            escapeshellarg($modules),
            $clean ? '--clean' : '',
            escapeshellarg($output),
        ));
        $cliOutput = new BufferedOutput();

        $cliCommand->run($cliInput, $cliOutput);
        echo $cliOutput->fetch();

        return true;
    }
}