<?php

namespace Orm\Annotate;

use Bitrix\Main\Localization\Loc;
use COption;
use Orm\Annotate\Exceptions\AnnotationException;

class Module
{
    const ID = 'orm.annotate';

    public static function getOption($name, $default = '')
    {
        return COption::GetOptionString(Module::ID, $name, $default);
    }

    public static function setOption($name, $value)
    {
        if ($value != COption::GetOptionString(Module::ID, $name)) {
            COption::SetOptionString(Module::ID, $name, $value);
        }
    }

    public static function removeOption($name)
    {
        COption::RemoveOption(Module::ID, $name);
    }

    public static function getDocRoot(): string
    {
        return rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR);
    }


    public static function checkHealth(): void
    {
        if (version_compare(PHP_VERSION, '8.1', '<')) {
            throw new AnnotationException(
                Loc::getMessage('ERR_PHP_NOT_SUPPORTED', [
                    '#NAME#' => PHP_VERSION,
                ])
            );
        }

        if (
            is_file($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . Module::ID . '/include.php')
            && is_file($_SERVER['DOCUMENT_ROOT'] . '/local/modules/' . Module::ID . '/include.php')
        ) {
            throw new AnnotationException(Loc::getMessage('ERR_MODULE_DUPLICATED'));
        }
    }
}



