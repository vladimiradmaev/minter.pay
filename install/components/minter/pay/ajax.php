<?php

use Bitrix\Main\Application;
use Bitrix\Main\Web\Json;
use Minter\Pay\Components\MinterPay;

define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);
define('DisableEventsCheck', true);
define('PUBLIC_AJAX_MODE', true);

require_once($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');

\CBitrixComponent::includeComponentClass('minter:pay');

$oApp = Application::getInstance();
$oRequest = $oApp->getContext()->getRequest();

if ($oRequest->isAjaxRequest() && check_bitrix_sessid()) {

    try {
        $oComponent = new \CBitrixComponent();
        $oComponent->initComponent('minter:pay');
        $oComponentClass = new MinterPay($oComponent);
        $sAction = $oRequest->get('ACTION');
        $arParams = $oRequest->get('PARAMS');
        switch ($sAction) {
            default:
                $oComponentClass->onPrepareComponentParams($arParams);
                break;
        }
        $APPLICATION->RestartBuffer();
        echo Json::encode(['RESULT' => $arResult]);
        die();

    } catch (\Exception $e) {
        $APPLICATION->RestartBuffer();
        echo json_encode([
            'ERROR' => $e->getMessage()
        ]);
    }
}