<?php
$path = "modules/orm.annotate/admin/orm_annotate_run.php";
foreach (['local', 'bitrix'] as $folder) {
    $path = join('/', [$_SERVER["DOCUMENT_ROOT"], $folder, $path]);
    if (is_file($path)) {
        require $path;
        break;
    }
}
