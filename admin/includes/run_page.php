<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Smolkov\Annotate\Exception\AnnotationException;
use Smolkov\Annotate\Executor;
use Smolkov\Annotate\Module;

global $APPLICATION;
$APPLICATION->SetTitle(Loc::getMessage("TAB_MAIN_TITLE"));

/**
 * Processing user actions
 */
$request = Bitrix\Main\Context::getCurrent()->getRequest();
if ($request->isPost()) {

    if (!check_bitrix_sessid()) {
        throw new AnnotationException(Loc::getMessage('ERR_CHECK_SESSID'));
    }

    $paramModules = trim((string)$request->getPost('modules'));

    if (!($paramModules <> "")) {
        throw new AnnotationException(Loc::getMessage('ERR_REQUIRED_PARAMETER', [
            'NAME' => Loc::getMessage('FIELD_MODULES_TITLE'),
        ]));
    }

    $output = trim((string)$request->getPost('output'));
    $clean = $request->getPost('clean') === 'Y';

    $annotator = new Executor($paramModules, $output, $clean);
    $strResult = $annotator->run();

    CAdminMessage::ShowNote($strResult);

    Module::setOption('modules', $paramModules);
    Module::setOption('output', $output);
    Module::setOption('clean', $clean);
}


/**
 * Define content of the settings page
 */

$modules = array_column(ModuleManager::getInstalledModules(), 'ID');
$modules = array_combine($modules, $modules);


$arSettings = [
    [
        'DIV' => 'main',
        'TAB' => Loc::getMessage('TAB_MAIN_TITLE'),
        'ICON' => 'form_settings',
        'TITLE' => Loc::getMessage('TAB_MAIN_TITLE'),
        'FIELDS' => [
            [
                'TITLE' => Loc::getMessage('FIELD_MODULES_TITLE'),
                'NAME' => 'modules',
                'TYPE' => 'select',
                'OPTIONS' => $modules,
                'VALUE' => Module::getOption('modules'),
            ],
            [
                'TITLE' => Loc::getMessage('FIELD_OUTPUT_TITLE'),
                'NAME' => 'output',
                'TYPE' => 'file_dialog',
                'VALUE' => Module::getOption('output'),
                'DEFAULT' => '/bitrix/modules/orm_annotations.php',
                'FILE_DIALOG_OPTIONS' => [
                    /** @see CAdminFileDialog::ShowScript */
                    'arPath' => ['PATH' => '/'],
                    'select' => 'DF',
                    'operation' => 'S',
                    'showUploadTab' => false,
                    'saveConfig' => true,
                    'showAddToMenuTab' => false,
                ],
            ],
            [
                'TITLE' => Loc::getMessage('FIELD_CLEAN_TITLE'),
                'NAME' => 'clean',
                'TYPE' => 'checkbox',
                'VALUE' => Module::getOption('clean'),
                'DEFAULT' => false,
            ],
        ],
        'BUTTONS' => [
            'btnApply' => true,
        ],
    ],
];