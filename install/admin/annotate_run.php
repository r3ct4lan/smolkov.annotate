<?php
$path = "modules/smolkov.annotate/admin/annotate_run.php";
foreach (['local', 'bitrix'] as $folder) {
    $path = join('/', [$_SERVER["DOCUMENT_ROOT"], $folder, $path]);
    if (is_file($path)) {
        require $path;
        break;
    }
}
