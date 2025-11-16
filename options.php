<?php

global $APPLICATION;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Orm\Annotate\Exceptions\AnnotationException;
use Orm\Annotate\Module;

$moduleId = 'orm.annotate';

try {

    if (!Loader::includeModule($moduleId)) {
        throw new Exception(Loc::getMessage('ERR_INCLUDE_MODULE', ['#NAME#' => $moduleId]));
    }

    if ($APPLICATION->GetGroupRight($moduleId) == 'D') {
        throw new Exception(Loc::getMessage("ERR_ACCESS_DENIED"));
    }

    Module::checkHealth();

    include __DIR__ . '/admin/includes/settings.php';
    include __DIR__ . '/admin/includes/templates/tab_control.php';

} catch (AnnotationException $e) {
    $arErrors = [$e->getMessage()];
    include __DIR__ . '/admin/includes/templates/errors.php';
}
