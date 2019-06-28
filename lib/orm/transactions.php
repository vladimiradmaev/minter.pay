<?php

namespace Minter\Pay\Orm;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;

/**
 * Class TransactionsTable
 * ОРМ-таблица списка транзакций
 * @package MinterPayModule
 */
class TransactionsTable extends DataManager
{
    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'minter_pay_transactions';
    }

    /**
     * {@inheritdoc}
     * @return array
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMap()
    {
        return [
            new IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            new IntegerField('ENTITY_ID', [
                'title' => Loc::getMessage("MINTER_PAY_HEADER_ENTITY_ID")
            ]),
            new StringField('ENTITY_NAME', [
                'title' => Loc::getMessage("MINTER_PAY_HEADER_ENTITY_NAME")
            ]),
            new StringField('WALLET', [
                'title' => Loc::getMessage("MINTER_PAY_HEADER_WALLET")
            ]),
            new IntegerField('USER_ID', [
                'title' => Loc::getMessage("MINTER_PAY_HEADER_USER_ID")
            ]),
            new IntegerField('PRICE', [
                'title' => Loc::getMessage("MINTER_PAY_HEADER_PRICE")
            ]),
            new DatetimeField('DATE_CREATE', [
                'title' => Loc::getMessage("MINTER_PAY_HEADER_DATE_CREATE")
            ]),
            new StringField('STATUS', [
                'title' => Loc::getMessage("MINTER_PAY_HEADER_STATUS"),
                'values' => ['SUCCESS', 'FALSE']
            ])
        ];
    }

    /**
     * Метод для получения статусов
     * @return array
     */
    public static function getStatusValues()
    {
        return [
            'SUCCESS' => Loc::getMessage("MINTER_PAY_STATUS_SUCCESS"),
            'FALSE' => Loc::getMessage("MINTER_PAY_STATUS_FALSE")
        ];
    }
}