<?php

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

/**
 * Class minter_pay
 */
class minter_pay extends CModule
{
    /**
     * minter_pay constructor.
     */
    public function __construct()
    {
        $arModuleVersion = [];

        include __DIR__ . '/version.php';
        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        $this->MODULE_ID = 'minter.pay';
        $this->MODULE_NAME = Loc::getMessage("MINTER_PAY_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("MINTER_PAY_MODULE_DESCRIPTION");
        $this->MODULE_GROUP_RIGHTS = 'N';
    }

    /**
     * Установка модуля
     * @throws Exception
     */
    public function doInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $this->doInstallTables();
        $this->doInstallEvent();
    }

    /**
     * Удаление модуля
     * @throws Exception
     */
    public function doUninstall()
    {
        $this->doUninstallTables();
        $this->doUninstallEvent();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * Регистрация событий
     */
    public function doInstallEvent()
    {
        return true;
    }

    /**
     * Добавление орм-таблиц
     */
    public function doInstallTables()
    {
        $oConnection = Application::getConnection();

        try {
            $sPath = __DIR__ . '/db/mysql/up.sql';
            if (file_exists($sPath)) {
                $sQuery = file_get_contents($sPath);
                $arSql = $oConnection->executeSqlBatch($sQuery, true);
                if (count($arSql)) {
                    throw new Exception(implode(PHP_EOL, $arSql));
                }
            } else {
                throw new Exception('Not found SQL file');
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Удаление орм-таблиц
     */
    public function doUninstallTables()
    {
        $oConnection = Application::getConnection();

        try {
            $sPath = __DIR__ . '/db/mysql/down.sql';
            if (file_exists($sPath)) {
                $sQuery = file_get_contents($sPath);
                $arSql = $oConnection->executeSqlBatch($sQuery, true);
                if (count($arSql)) {
                    throw new Exception(implode(PHP_EOL, $arSql));
                }
            } else {
                throw new Exception('Not found SQL file');
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Удаление событий
     */
    public function doUninstallEvent()
    {
        return true;
    }

    /**
     * Получение директории
     *
     * @param bool $bNotDocumentRoot
     * @return mixed|string
     */
    public function getPath($bNotDocumentRoot = false)
    {
        if ($bNotDocumentRoot) {
            return str_ireplace(Application::getDocumentRoot(), '', str_replace('\\', '/', dirname(__DIR__)));
        }

        return dirname(__DIR__);
    }
}