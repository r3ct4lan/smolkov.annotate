<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin.php");

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Orm\Annotate\Exception\AnnotationException;
use Orm\Annotate\Module;

$moduleId = 'orm.annotate';

try {
    if (!Loader::includeModule($moduleId)) {
        throw new Exception(Loc::getMessage('ERR_INCLUDE_MODULE', ['#NAME#' => $moduleId]));
    }

    Module::checkHealth();

    include __DIR__ . '/includes/run_page.php';
    include __DIR__ . '/includes/templates/tab_control.php';

} catch (AnnotationException|LoaderException $e) {
    $arErrors = [$e->getMessage()];
    include __DIR__ . '/includes/templates/errors.php';
}

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
