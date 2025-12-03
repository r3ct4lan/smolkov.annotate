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
                "TITLE" => Loc::getMessage('FIELD_MODULES_TITLE'),
                "NAME" => "modules",
                "TYPE" => "select",
                "OPTIONS" => $modules,
            ],
            [
                "TITLE" => Loc::getMessage('FIELD_OUTPUT_TITLE'),
                "NAME" => "output",
                "TYPE" => "file_dialog",
                "DEFAULT" => "/bitrix/modules/orm_annotations.php",
                "FILE_DIALOG_OPTIONS" => [
                    /** @see CAdminFileDialog::ShowScript */
                    "arPath" => ["PATH" => '/'],
                    "select" => 'DF',
                    "operation" => 'S',
                    "showUploadTab" => false,
                    "saveConfig" => true,
                    "showAddToMenuTab" => false,
                ],
            ],
            [
                "TITLE" => Loc::getMessage('FIELD_CLEAN_TITLE'),
                "NAME" => "clean",
                "TYPE" => "checkbox",
                "DEFAULT" => false,
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

        $annotator = new Executor($modules, $output, $clean);
        $strResult = $annotator->run();

        CAdminMessage::ShowNote($strResult);

    }
}

