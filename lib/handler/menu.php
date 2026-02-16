<?php

namespace Smolkov\Annotate\Handler;

use Bitrix\Main\Localization\Loc;

class Menu
{
    /**
     * @noinspection PhpUnusedParameterInspection
     */
    public static function onBuildGlobalMenuHandler(array &$globalMenu, array &$arMenu): void
    {
        $moduleMenu = [
            'text' => Loc::getMessage('MENU_GROUP_TITLE'),
            'title' => Loc::getMessage('MENU_GROUP_TITLE'),
            'items_id' => 'smolkov_annotate',
            'items' => [
                [
                    'text' => Loc::getMessage('MENU_ITEM_RUN_TITLE'),
                    'title' => Loc::getMessage('MENU_ITEM_RUN_TITLE'),
                    'url' => 'annotate_run.php',
                ],
            ],
        ];

        foreach ($arMenu as $key => $menuGroup) {
            if ($menuGroup['section'] === 'TOOLS') {
                $arMenu[$key]['items'][] = $moduleMenu;
            }
        }
    }
}