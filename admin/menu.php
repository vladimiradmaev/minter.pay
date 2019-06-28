<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

AddEventHandler('main', 'OnBuildGlobalMenu', 'MinterPayMenu');

/**
 * @param $adminMenu
 * @param $moduleMenu
 */
function MinterPayMenu(&$adminMenu, &$moduleMenu)
{
    $adminMenu['global_menu_services']['items'][] = [
        'section' => 'minter-pay',
        'sort' => 110,
        'text' => 'Криптоплатформа Minter',
        'icon' => 'learning-menu-icon',
        'page_icon' => 'learning-menu-icon',
        'items_id' => 'minter-pay-transactions',
        "items" => [
            [
                "parent_menu" => "minter-pay",
                "section" => "minter-pay-transactions",
                "sort" => 500,
                "url" => "minter.pay_transactions.php?lang=" . LANG,
                "text" => 'Транзакции оплаты',
                "title" => 'Список транзакций оплаты товаров и услуг через криптоплатформу Minter',
                "icon" => "form_menu_icon",
                "page_icon" => "form_page_icon",
                "items_id" => "minter-pay-transactions"
            ],
        ]
    ];
}
