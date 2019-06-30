<?php

namespace Minter\Pay\Components;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
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
        $arResult = [];
        if ($sWallet) {
            try {
                $oApi = new MinterAPI($this->sUrl);
                $oResponseApi = $oApi->getBalance($sWallet);
                foreach ($oResponseApi->data->balances as $oBalance) {
                    $arResult['WALLET_INFO']['COINS'][$oBalance->coin] = $oBalance->amount;
                }
                $arResult['WALLET_INFO'] = $oResponseApi;
            } catch (RequestException $exception) {
                $arResult['ERRORS'] = $exception->getMessage();
            }
        } else {
            $arResult = ['ERRORS' => Loc::getMessage("MINTER_PAY_WALLET_ERROR_EMPTY_WALLET")];
        }
        return $arResult;
    }
}