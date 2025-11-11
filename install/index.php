<?php

use Bitrix\Main\ModuleManager;

class orm_annotate extends CModule
{
    public function __construct()
    {
        $props = require __DIR__ . DIRECTORY_SEPARATOR . 'version.php';
        foreach ($props as $k => $v) {
            $this->$k = $v;
        }
    }

    public function DoInstall(): void
    {
        ModuleManager::registerModule($this->MODULE_ID);
    }

    public function DoUninstall(): void
    {
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}
