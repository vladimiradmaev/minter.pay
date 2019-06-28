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
     * @var array Исключения при копировании файлов административного раздела
     */
    public $arExclusionAdminFiles;

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

        $this->arExclusionAdminFiles = [
            '..',
            '.',
            'menu.php',
        ];

        $this->MODULE_ID = 'minter.pay';
        $this->MODULE_NAME = Loc::getMessage("MINTER_PAY_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("MINTER_PAY_MODULE_DESCRIPTION");
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = 'Vladimir Admaev';
        $this->PARTNER_URI = 'https://github.com/vladimiradmaev';
    }

    /**
     * Установка модуля
     * @throws Exception
     */
    public function doInstall()
    {
        if (class_exists('Minter\MinterAPI')) {
            ModuleManager::registerModule($this->MODULE_ID);
            $this->doInstallTables();
            $this->doInstallFiles();
            $this->doInstallEvent();
        } else {
            throw new \Exception(Loc::getMessage("MINTER_PAY_ERROR_COMPOSER_DEPENDENCE"));
        }
    }

    /**
     * Удаление модуля
     * @throws Exception
     */
    public function doUninstall()
    {
        $this->doUninstallTables();
        $this->doUninstallFiles();
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
     * Метод инициализирует инсталяцию файлов модуля
     */
    public function doInstallFiles()
    {
        if (\Bitrix\Main\IO\Directory::isDirectoryExists($sPath = $this->GetPath() . '/admin')) {
            if ($oDir = opendir($sPath)) {
                while (false !== $oItem = readdir($oDir)) {
                    if (in_array($oItem, $this->arExclusionAdminFiles)) {
                        continue;
                    }
                    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $oItem,
                        '<' . '? require($_SERVER["DOCUMENT_ROOT"]."' . $this->GetPath(true) . '/admin/' . $oItem . '");?' . '>');
                }
                closedir($oDir);
            }
        }

        if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . '/assets')) {
            CopyDirFiles($this->GetPath() . "/assets/",
                $_SERVER["DOCUMENT_ROOT"] . "/bitrix/themes/" . $this->MODULE_ID,
                true, true);
        }

        if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . '/install/components/')) {
            CopyDirFiles($path,
                $_SERVER["DOCUMENT_ROOT"] . "/local/components/",
                true, true);
        }

        return true;
    }

    /**
     * Метод инициализирует деинсталяцию файлов модуля
     */
    public function doUninstallFiles()
    {
        if (\Bitrix\Main\IO\Directory::isDirectoryExists($sPath = $this->GetPath() . '/admin')) {
            DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . $this->GetPath() . '/admin/',
                $_SERVER["DOCUMENT_ROOT"] . '/bitrix/admin');
            if ($oDir = opendir($sPath)) {
                while (false !== $oItem = readdir($oDir)) {
                    if (in_array($oItem, $this->arExclusionAdminFiles)) {
                        continue;
                    }
                    \Bitrix\Main\IO\File::deleteFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $oItem);
                }
                closedir($oDir);
            }
        }

        if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . '/assets')) {
            DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . $this->GetPath() . '/assets/',
                $_SERVER["DOCUMENT_ROOT"] . '/bitrix/themes/' . $this->MODULE_ID);
        }

        if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . '/install/components/')) {
            $arComponents = scandir($path . '/minter/');
            foreach ($arComponents as $component) {
                if ($component == '.' || $component == '..') {
                    continue;
                }
                DeleteDirFilesEx('/local/components/minter/' . $component . '/');
            }
            rmdir($_SERVER['DOCUMENT_ROOT'] . '/local/components/minter/');
        }

        return true;
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