<?php

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Entity;
use Bitrix\Main\SystemException;
use Orm\Annotate\Model\TaskTable;

class orm_annotate extends CModule
{
    public function __construct()
    {
        $props = require __DIR__ . DIRECTORY_SEPARATOR . 'version.php';
        foreach ($props as $k => $v) {
            $this->$k = $v;
        }
    }

    private array $models = [
        TaskTable::class,
    ];

    /**
     * @throws LoaderException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function DoInstall(): void
    {
        ModuleManager::registerModule($this->MODULE_ID);
        Loader::includeModule($this->MODULE_ID);
        $this->InstallDB();
    }

    /**
     * @throws LoaderException
     * @throws ArgumentException
     * @throws SqlQueryException
     * @throws SystemException
     */
    public function DoUninstall(): void
    {
        Loader::includeModule($this->MODULE_ID);
        $this->UnInstallDB();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * @throws ArgumentException
     * @throws SystemException
     */
    public function InstallDB(): void
    {
        foreach ($this->models as $modelClass) {
            $instance = Entity::getInstance($modelClass);
            /* @var $class DataManager */
            $tableName = $instance->getDBTableName();
            $class = new $modelClass;
            $connection = Application::getConnection($class::getConnectionName());
            if (!$connection->isTableExists($tableName)) {
                $instance->createDbTable();
            }
        }
    }

    /**
     * @throws ArgumentException
     * @throws SystemException
     * @throws SqlQueryException
     */
    public function UnInstallDB(): void
    {
        foreach ($this->models as $modelClass) {
            $instance = Entity::getInstance($modelClass);
            /* @var $class DataManager */
            $tableName = $instance->getDBTableName();
            $class = new $modelClass;
            $connection = Application::getConnection($class::getConnectionName());
            if ($connection->isTableExists($tableName)) {
                $connection->dropTable($tableName);
            }
        }
    }
}
