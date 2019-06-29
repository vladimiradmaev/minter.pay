<?php

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use GuzzleHttp\Exception\RequestException;

Loc::loadMessages(__FILE__);

$arDefaultOptions = require_once "default_option.php";

defined('ADMIN_MODULE_NAME') or define('ADMIN_MODULE_NAME', 'minter.pay');

Loader::includeModule(ADMIN_MODULE_NAME);

if (!$USER->isAdmin()) {
    $APPLICATION->authForm('Nope');
}

$oApp = Application::getInstance();
$oContext = $oApp->getContext();
$oRequest = $oContext->getRequest();

Loc::loadMessages($oContext->getServer()->getDocumentRoot() . "/bitrix/modules/main/options.php");

$arErrors = [];

$arTabs = [
    [
        "DIV" => "edit1",
        "TAB" => Loc::getMessage("MAIN_TAB_SET"),
        "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_SET"),
        'OPTIONS' => [
            [
                'WALLET',
                Loc::getMessage("MINTER_PAY_SETTINGS_WALLET"),
                $arDefaultOptions['WALLET'],
                ['text', 30]
            ],
            ["note" => Loc::getMessage('OPTIONS_DESCRIPTION')],
        ]
    ],
];

$oTabControl = new CAdminTabControl("tabControl", $arTabs);

if ((!empty($save) || !empty($restore)) && $oRequest->isPost() && check_bitrix_sessid()) {
    if (!empty($restore)) {
        Option::delete(ADMIN_MODULE_NAME);
        CAdminMessage::showMessage([
            "MESSAGE" => Loc::getMessage("REFERENCES_OPTIONS_RESTORED"),
            "TYPE" => "OK",
        ]);
    } else {
        foreach ($arTabs as $arTab) {
            foreach ($arTab['OPTIONS'] as $arOption) {
                if ($arOption[0] === 'SERVICE_ADDRESS' && $oRequest->get('SERVICE_ADDRESS') == '') {
                    CAdminMessage::showMessage(Loc::getMessage("REFERENCES_INVALID_VALUE",
                        ['#FIELD#' => $arOption[1]]));
                    continue;
                }
                __AdmSettingsSaveOption(ADMIN_MODULE_NAME, $arOption);
            }
        }
    }

    /**
     * Minter Wallet test
     */
    $arFields['WALLET'] = $oRequest->get('WALLET');
    if (strlen($arFields["WALLET"]) > 0 && $save != "" || $apply != "") {
        if (!class_exists('\Minter\MinterAPI')) {
            $arErrors[] = Loc::getMessage('MINTER_PAY_ERROR_COMPOSER_DEPENDENCE');
        } else {
            $sUrl = 'https://explorer-api.apps.minter.network';
            $oApi = new Minter\Pay\MinterAPI($sUrl);
            try {
                $oResponseApi = $oApi->getBalance($arFields["WALLET"]);
                $iMinterWalletNotifyID = CAdminNotify::GetList(
                    ['ID' => 'ASC'],
                    [
                        'MODULE_ID' => ADMIN_MODULE_NAME,
                        'TAG' => 'minter_wallet_error'
                    ]
                )->GetNext()['ID'];

                if ($iMinterWalletNotifyID) {
                    CAdminNotify::Delete($iMinterWalletNotifyID);
                }
            } catch (RequestException $exception) {
                CAdminNotify::Add(array(
                    'MESSAGE' => Loc::getMessage("MINTER_PAY_OPTIONS_ERROR_WALLET"),
                    'TAG' => 'minter_wallet_error',
                    'MODULE_ID' => ADMIN_MODULE_NAME,
                    'ENABLE_CLOSE' => 'Y',
                ));
                $arErrors[] = $exception->getMessage();
            }
        }
    }
}

if (count($arErrors) > 0) {
    CAdminMessage::ShowMessage([
        "MESSAGE" => implode("\n", $arErrors),
        "HTML" => true,
        "TYPE" => "ERROR",
    ]);
}

$oTabControl->begin();
?>

<form method="post"
      action="<?= sprintf('%s?mid=%s&lang=%s', $oRequest->getRequestedPage(), urlencode($mid), LANGUAGE_ID) ?>">
    <?php
    foreach ($arTabs as $arTab) {
        if ($arTab['OPTIONS']) {
            $oTabControl->BeginNextTab();
            __AdmSettingsDrawList(ADMIN_MODULE_NAME, $arTab['OPTIONS']);
        }
    }
    $oTabControl->beginNextTab();
    $oTabControl->buttons();
    ?>
    <input type="submit"
           name="save"
           value="<?= Loc::getMessage("MAIN_SAVE") ?>"
           title="<?= Loc::getMessage("MAIN_OPT_SAVE_TITLE") ?>"
           class="adm-btn-save"
    />
    <input type="submit"
           name="restore"
           title="<?= Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
           onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
           value="<?= Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>"
    />
    <?php
    echo bitrix_sessid_post();
    $oTabControl->end();
    ?>
</form>