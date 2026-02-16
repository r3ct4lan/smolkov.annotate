<?php

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Entity;
use Bitrix\Main\SystemException;
use Smolkov\Annotate\Model\TaskTable;

class smolkov_annotate extends CModule
{
    var $MODULE_ID = "smolkov.annotate";
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $PARTNER_NAME;
    var $PARTNER_URI;

    public function __construct()
    {
        $arModuleVersion = [];

        include(__DIR__ . "/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME = Loc::getMessage("SMOLKOV_ANNOTATE_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("SMOLKOV_ANNOTATE_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = Loc::getMessage("SMOLKOV_ANNOTATE_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("SMOLKOV_ANNOTATE_PARTNER_URI");
    }

    private array $models = [
        TaskTable::class,
    ];

    private array $filesList = [
        'admin' => [
            'target' => '/bitrix/admin',
            'rewrite' => false,
        ],
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
        $this->InstallFiles();
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
        $this->UnInstallFiles();
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

        $eventManager = EventManager::getInstance();
        $eventManager->registerEventHandlerCompatible(
            'main',
            'OnBuildGlobalMenu',
            $this->MODULE_ID,
            /** @see \Smolkov\Annotate\Handler\Menu::onBuildGlobalMenuHandler */
            '\\Smolkov\\Annotate\\Handler\\Menu',
            'onBuildGlobalMenuHandler'
        );
    }

    public function InstallFiles(): void
    {
        foreach ($this->getFilesList() as $config) {
            CopyDirFiles(
                $config['from'],
                $config['target'],
                $config['rewrite'],
                true
            );
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

        $eventManager = EventManager::getInstance();
        $eventManager->unRegisterEventHandler(
            "main",
            "OnBuildGlobalMenu",
            $this->MODULE_ID,
            '\\Smolkov\\Annotate\\Handler\\Menu',
            'onBuildGlobalMenuHandler'
        );
    }

    public function UnInstallFiles(): void
    {
        foreach ($this->getFilesList() as $config) {
            DeleteDirFiles($config['from'], $config['target']);
        }
    }

    private function getFilesList(): array
    {
        $result = [];

        $moduleDir = explode(DIRECTORY_SEPARATOR, __DIR__);
        array_pop($moduleDir);
        $moduleDir = implode(DIRECTORY_SEPARATOR, $moduleDir);

        $sourceRoot = $moduleDir . '/install/';
        $targetRoot = $_SERVER['DOCUMENT_ROOT'];

        foreach ($this->filesList as $from => $config) {
            $result[$from] = array_merge($config, [
                'from' => $sourceRoot . $from,
                'target' => $targetRoot . $config['target'],
            ]);
        }
        return $result;
    }
}
