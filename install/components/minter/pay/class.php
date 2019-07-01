<?php

namespace Minter\Pay\Components;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use GuzzleHttp\Exception\RequestException;
use Minter\Pay\MinterAPI;

class MinterPay extends \CBitrixComponent
{
    /**
     * @var string $sModuleId "ID модуля"
     */
    public $sModuleId = "minter.pay";

    /**
     * @var string API-url Minter
     */
    protected $sUrl = 'https://explorer-api.apps.minter.network';

    /**
     * @param $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {
        return parent::onPrepareComponentParams($arParams);
    }

    /**
     * @return mixed|void
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    public function executeComponent()
    {
        \CJSCore::Init(['jquery']);
        //Asset::getInstance()->addCss("/bitrix/css/main/bootstrap.min.css");
        $this->arResult['WALLET'] = Option::get($this->sModuleId, "WALLET");

        $this->includeComponentTemplate();
    }

    /**
     * Метод для получения доступных монет пользователя
     * @param $sWallet string Кошелёк пользователя
     * @return array
     * @throws \Exception
     */
    public function getWalletBalance($sWallet)
    {
        Loader::includeModule('minter.pay');

        $arResult = [];
        if ($sWallet) {
            try {
                $oApi = new MinterAPI($this->sUrl);
                $oResponseApi = $oApi->getBalance($sWallet);
                foreach ($oResponseApi->data->balances as $oBalance) {
                    $arResult['WALLET_INFO']['COINS'][$oBalance->coin] = $oBalance->amount;
                }
            } catch (RequestException $exception) {
                if ($exception->getCode() == 422) {
                    $arResult['ERRORS'] = Loc::getMessage("MINTER_PAY_WALLET_ERROR_WRONG_WALLET");
                }
            }
        } else {
            $arResult = ['ERRORS' => Loc::getMessage("MINTER_PAY_WALLET_ERROR_EMPTY_WALLET")];
        }
        return $arResult;
    }

    /**
     * Получение курса выбранной монеты
     * @param $sCoin
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public function getCoinRate($sCoin)
    {
        Loader::includeModule('minter.pay');

        $arResult = [];
        if ($sCoin) {
            try {
                $oApi = new MinterAPI('https://minterscan.pro');
                $arResult['COIN_RATE'] = $oApi->getRate($sCoin, 'BIP')->result->will_get;
            } catch (RequestException $exception) {
                $arResult['ERRORS'] = $exception->getMessage();
            }
        } else {
            $arResult = ['ERRORS' => Loc::getMessage("MINTER_PAY_WALLET_ERROR_EMPTY_WALLET")];
        }
        return $arResult;
    }
}