<?php

namespace Orm\Annotate;

use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Orm\Annotate\Exceptions\AnnotationException;

class Module
{
    const ID = ORM_ANNOTATE_MODULE_ID;

    public static function getOption($name, $default = ''): string
    {
        return Option::get(Module::ID, $name, $default);
    }

    /**
     * @throws ArgumentOutOfRangeException
     */
    public static function setOption($name, $value): void
    {
        if ($value != static::getOption($name)) {
            Option::set(Module::ID, $name, $value);
        }
    }

    public static function getDocRoot(): string
    {
        return rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR);
    }


    /**
     * @throws AnnotationException
     */
    public static function checkHealth(): void
    {
        global $APPLICATION;

        if ($APPLICATION->GetGroupRight(Module::ID) == 'D') {
            throw new AnnotationException(Loc::getMessage('ERR_ACCESS_DENIED'));
        }

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



