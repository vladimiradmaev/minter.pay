<button class="btn btn-primary" id="minter-pay">Оплатить через Minter</button>
<div id="modal_form">
    <span id="modal_close">X</span>
    <div class="form-group text-center">
        <label for="wallet">Кошелёк</label>
        <textarea class="form-control" id="wallet"></textarea>
    </div>
    <div class="form-group text-center hidden coins">
        <label for="coins">Доступные монеты</label>
        <select id="coins"></select>
    </div>
    <div class="form-group text-center hidden coins-rate">
        <span class="coin-rate"></span>
    </div>
    <div class="form-group text-center">
        <p class="response"></p>
    </div>
    <button class="btn btn-success">Получить информацию о кошельке</button>
    <button class="btn btn-success hidden" id="agree">Подтвердить</button>
    <button class="btn btn-warning" id="cancel">Отмена</button>
</div>

<div id="overlay"></div>