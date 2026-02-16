<?php

namespace Smolkov\Annotate\Model;

use Bitrix\Main\ArgumentTypeException;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\SystemException;
use Smolkov\Annotate\Helper\Output;

class TaskTable extends DataManager
{
    const MODULES_ALL = 'all';

    public static function getTableName(): string
    {
        return 'smolkov_annotate_task';
    }

    /**
     * @return array
     * @throws ArgumentTypeException
     * @throws SystemException
     */
    public static function getMap(): array
    {
        return [
            (new Fields\IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new Fields\StringField('MODULES'))
                ->configureRequired()
                ->configureDefaultValue(self::MODULES_ALL)
                ->addValidator(new Fields\Validators\LengthValidator(1))
                ->addValidator(function ($value) {
                    $arValue = array_map('trim', explode(",", $value));
                    $existedModules = ModuleManager::getInstalledModules();
                    $arCheck = array_column($existedModules, 'ID');
                    $arCheck[] = self::MODULES_ALL;
                    return empty(array_diff($arValue, $arCheck));
                }),

            (new Fields\StringField('OUTPUT'))
                ->configureRequired()
                ->configureDefaultValue(Output::DEFAULT_VALUE)
                ->configureUnique()
                ->addValidator(new Fields\Validators\LengthValidator(1))
                ->addValidator(new Fields\Validators\RegExpValidator(
                    '/^[\\/]?[\w.\-]+(?:[\\/][\w.\-]+)*\.php$/'
                )),

            (new Fields\BooleanField('CLEAN'))
                ->configureDefaultValue(false),
        ];
    }
}