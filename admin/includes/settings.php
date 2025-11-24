<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Orm\Annotate\Exceptions\AnnotationException;
use Orm\Annotate\Executor;

/**
 * Define content of the settings page
 */

$modules = array_column(ModuleManager::getInstalledModules(), 'ID');
$modules = array_combine($modules, $modules);


$arSettings = [
    [
        "DIV" => "main",
        "TAB" => Loc::getMessage('TAB_MAIN_TITLE'),
        "ICON" => "form_settings",
        "TITLE" => Loc::getMessage('TAB_MAIN_TITLE'),
        "FIELDS" => [
            [
                "TITLE" => "modules",
                "NAME" => "modules",
                "TYPE" => "select",
                "OPTIONS" => $modules,
            ],
            [
                "TITLE" => "output",
                "NAME" => "output",
                "TYPE" => "text",
            ],
            [
                "TITLE" => "clean",
                "NAME" => "clean",
                "TYPE" => "checkbox",
            ],
        ],
        "BUTTONS" => [
            'btnApply' => true,
        ],
    ],
];

/**
 * Processing user actions
 */
$request = Bitrix\Main\Context::getCurrent()->getRequest();
if ($request->isPost()) {

    if (!check_bitrix_sessid()) {
        throw new AnnotationException(Loc::getMessage('ERR_CHECK_SESSID'));
    }

    $modules = trim((string)$request->getPost('modules'));

    if ($modules <> "") {

        $output = trim((string)$request->getPost('output'));
        $clean = $request->getPost('clean') === 'Y';

        $strResult = Executor::run($modules, $output, $clean);

        CAdminMessage::ShowNote($strResult);

    }
}

