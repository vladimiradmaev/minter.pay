<button class="btn btn-open" id="minter-pay">Оплатить через Minter</button>
<div id="modal_form">
    <span id="modal_close">X</span>
    <div class="form-group">
        <label for="wallet">Кошелёк</label>
        <textarea class="form-control" id="wallet"></textarea>
    </div>
    <div id="coins-wrapper" class="form-group hidden">
        <label for="coins">Доступные монеты</label>
        <select id="coins"></select>
    </div>
    <div id="coin-rate-wrapper" class="form-group text-center hidden">
        <span id="coin-rate"></span>
    </div>
    <div class="form-group text-center">
        <p id="response"></p>
    </div>
    <button class="btn btn-success" id="load-wallet">Получить информацию о кошельке</button>
    <div class="form-group text-center"></div>
    <div class="btn-wrapper">
        <button class="btn btn-success hidden" id="pay">Подтвердить</button>
        <button class="btn btn-warning" id="cancel">Отмена</button>
    </div>
</div>

<div id="overlay"></div>

<script>
    BX.ready(function () {
        var arParams = <?=CUtil::PhpToJSObject([
            'AJAX_PATH' => $componentPath . '/ajax.php'
        ])?>;
        new BX.Minter.Pay(arParams);
    });
</script>