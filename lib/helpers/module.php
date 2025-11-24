<?php

namespace Orm\Annotate\Helpers;

class Module
{
    private string $value;

    public function __construct(?string $moduleName = null)
    {
        $this->value = trim((string)$moduleName);
    }

    public function getDirectory(): string
    {
        $root = rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR);

        $checkFile = join(DIRECTORY_SEPARATOR, [
            $root,
            'local',
            'modules',
            $this->value,
            'include.php'
        ]);

        return join(DIRECTORY_SEPARATOR, [
            $root,
            is_file($checkFile) ? 'local' : 'bitrix',
            'modules',
            $this->value,
        ]);
    }
}