<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';

try {
    define('MODULE_ID', 'minter.pay');
    Loader::includeModule(MODULE_ID);

    Loc::loadMessages(__FILE__);

    /** @var CMain $APPLICATION */
    global $APPLICATION;

    $sPostRight = $APPLICATION->GetGroupRight(MODULE_ID);
    if ($sPostRight == 'D') {
        $APPLICATION->AuthForm(Loc::getMessage("MINTER_PAY_ACCESS_DENIED"));
    }

    $oRequest = Application::getInstance()->getContext()->getRequest();
    $sTableID = Minter\Pay\Orm\TransactionsTable::getTableName();
    $oSort = new CAdminSorting($sTableID, 'DATE_CREATE', 'desc');
    $lAdmin = new CAdminList($sTableID, $oSort);

    function CheckFilter()
    {
        global $FilterArr, $lAdmin;
        foreach ($FilterArr as $f) {
            global $$f;
        }

        return count($lAdmin->arFilterErrors) == 0; // если ошибки есть, вернем false;
    }

    $FilterArr = [
        'ID' => 'find_id',
        'ENTITY_ID' => 'find_entity_id',
        'WALLET' => 'find_wallet',
        'USER_ID' => 'find_user_id',
        'DATE_CREATE' => 'find_date_create',
        'STATUS' => 'find_status'
    ];

    $lAdmin->InitFilter($FilterArr);

    if (CheckFilter()) {
        $setFilter = $lAdmin->getFilter();
        foreach ($FilterArr as $filter => $value) {
            if (isset($setFilter[$value]) && !empty($setFilter[$value])) {
                $arFilter[$filter] = $setFilter[$value];
            }
        }
    }

    $arTableHeaders = [
        [
            'id' => 'ID',
            'content' => 'ID',
            'sort' => 'ID',
            'default' => true,
        ],
        [
            'id' => 'ENTITY_ID',
            'content' => Loc::getMessage("MINTER_PAY_HEADER_ENTITY_ID"),
            'sort' => 'ENTITY_ID',
            'default' => true,
        ],
        [
            'id' => 'ENTITY_NAME',
            'content' => Loc::getMessage("MINTER_PAY_HEADER_ENTITY_NAME"),
            'sort' => false,
            'default' => true,
        ],
        [
            'id' => 'WALLET',
            'content' => Loc::getMessage("MINTER_PAY_HEADER_WALLET"),
            'sort' => false,
            'default' => true,
        ],
        [
            'id' => 'USER_ID',
            'content' => Loc::getMessage("MINTER_PAY_HEADER_USER_ID"),
            'sort' => false,
            'default' => true,
        ],
        [
            'id' => 'DATE_CREATE',
            'content' => Loc::getMessage("MINTER_PAY_HEADER_DATE_CREATE"),
            'sort' => 'DATE_CREATE',
            'default' => true,
        ],
        [
            'id' => 'PRICE',
            'content' => Loc::getMessage("MINTER_PAY_HEADER_PRICE"),
            'sort' => 'PRICE',
            'default' => true,
        ],
        [
            'id' => 'STATUS',
            'content' => Loc::getMessage("MINTER_PAY_HEADER_STATUS"),
            'sort' => 'STATUS',
            'default' => true,
        ]
    ];
    $lAdmin->AddHeaders($arTableHeaders);

    //Вывод навигации
    $obAllData = Minter\Pay\Orm\TransactionsTable::getList([
        'filter' => (!empty($arFilter) ? $arFilter : []),
        'select' => ['ID']
    ]);
    $rsAllData = new CAdminResult($obAllData, $sTableID);
    $rsAllData->NavStart();
    $lAdmin->NavText($rsAllData->GetNavPrint(Loc::getMessage("MINTER_PAY_NAV_TEXT")));

    //Запрос данных для таблицы
    $arRequest = [];
    if (!empty($arFilter)) {
        $arRequest['filter'] = $arFilter;
    }
    $arRequest['select'] = $lAdmin->GetVisibleHeaderColumns();
    $arRequest['order'] = [$oSort->getField() => $oSort->getOrder()];
    if (!$rsAllData->NavShowAll) {//Ограничения выборки с учетом навигации
        $arRequest['limit'] = $rsAllData->NavPageSize;
        if ((int)$rsAllData->NavPageNomer > 1) {
            $arRequest['offset'] = ($rsAllData->NavPageNomer - 1) * $rsAllData->NavPageSize;
        }
    }
    $oData = Minter\Pay\Orm\TransactionsTable::getList($arRequest);
    $rsData = new CAdminResult($oData, $sTableID);

    while ($arRes = $rsData->NavNext(true, 'f_')) {
        $row =& $lAdmin->AddRow($f_ID, $arRes);
    }

    $arStatuses = Minter\Pay\Orm\TransactionsTable::getStatusValues();

    $lAdmin->CheckListMode();

    $APPLICATION->SetTitle(Loc::getMessage("MINTER_PAY_TRANSACTIONS_TITLE"));
} catch (Exception $e) {
    $oError = $e->getMessage();
}

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';

if (isset($oError)) {
    CAdminMessage::ShowMessage([
        'MESSAGE' => $oError,
        'TYPE' => 'ERROR',
    ]);
}

$oFilter = new CAdminFilter(
    $sTableID . '_filter',
    [
        'ID',
        Loc::getMessage("MINTER_PAY_HEADER_ENTITY_ID"),
        Loc::getMessage("MINTER_PAY_HEADER_WALLET"),
        Loc::getMessage("MINTER_PAY_HEADER_USER_ID"),
        Loc::getMessage("MINTER_PAY_HEADER_DATE_CREATE"),
        Loc::getMessage("MINTER_PAY_HEADER_STATUS")
    ]
);
?>
<form name="find_form" method="get" action="<? echo $APPLICATION->GetCurPage(); ?>">
    <? $oFilter->Begin(); ?>
    <tr>
        <td>ID:</td>
        <td>
            <input type="text" name="find_id" size="47" value="<? echo htmlspecialchars($find_id) ?>">
        </td>
    </tr>
    <tr>
        <td><?= Loc::getMessage("MINTER_PAY_HEADER_ENTITY_ID") ?></td>
        <td>
            <input type="text" name="find_entity_id" size="47" value="<? echo htmlspecialchars($find_entity_id) ?>">
        </td>
    </tr>
    <tr>
        <td><?= Loc::getMessage("MINTER_PAY_HEADER_WALLET") ?></td>
        <td>
            <input type="text" name="find_wallet" size="47" value="<? echo htmlspecialchars($find_wallet) ?>">
        </td>
    </tr>
    <tr>
        <td><?= Loc::getMessage("MINTER_PAY_HEADER_USER_ID") ?></td>
        <td>
            <input type="text" name="find_user_id" size="47" value="<? echo htmlspecialchars($find_user_id) ?>">
        </td>
    </tr>
    <tr>
        <td><?= Loc::getMessage("MINTER_PAY_HEADER_DATE_CREATE") ?></td>
        <td>
            <input type="text" name="find_date_create" size="47" value="<? echo htmlspecialchars($find_date_create) ?>">
        </td>
    </tr>
    <tr>
        <td><?= Loc::getMessage("MINTER_PAY_HEADER_STATUS") ?></td>
        <td>
            <select name="find_status" id="">
                <?php foreach ($arStatuses as $sStatus => $sStatusName): ?>
                    <option value="<?= $sStatus ?>"><?= $sStatusName ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <?
    $oFilter->Buttons(["table_id" => $sTableID, "url" => $APPLICATION->GetCurPage(), "form" => "find_form"]);
    $oFilter->End();
    ?>
</form>

<? $lAdmin->DisplayList(); ?>


