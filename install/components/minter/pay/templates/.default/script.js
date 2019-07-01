BX.ready(function () {
    BX.namespace('Minter.Pay');
    BX.Minter.Pay = function(arParams) {
        $('#minter-pay').click(function () {
            $('#overlay').fadeIn(400,
                function () {
                    $('#modal_form')
                        .css('display', 'block')
                        .animate({opacity: 1, top: '45%'}, 200);
                });
        });

        $('#modal_close, #overlay').click(function () {
            $('#modal_form')
                .animate({opacity: 0, top: '45%'}, 200,
                    function () {
                        $(this).css('display', 'none');
                        $('#overlay').fadeOut(400);
                    }
                );
        });

        /**
         * Закрытие попапа
         */
        BX.bind(BX('cancel'), 'click', function () {
            BX.fireEvent(BX('modal_close'), 'click');
        });

        /**
         * Загрузка информации о кошельке
         */
        BX.bind(BX('load-wallet'), 'click', function () {
            var sWallet = BX('wallet').value;
            BX('response').innerHTML = '';
            if (!BX.hasClass(BX('coins-wrapper'), 'hidden')) {
                BX.selectUtils.deleteAllOptions(BX('coins'));
                BX.addClass(BX('coins-wrapper'), 'hidden');
            }
            if (sWallet) {
                BX.ajax({
                    url: arParams['AJAX_PATH'],
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        ACTION: 'getWalletCoins',
                        WALLET: sWallet,
                        sessid: BX.bitrix_sessid()
                    },
                    onsuccess: function (response) {
                        if (response.RESULT) {
                            if (response.RESULT.ERRORS) {
                                BX('response').innerHTML = '<span class="error">' + response.RESULT.ERRORS + '</span>';
                            } else {
                                var oCoins = response.RESULT.WALLET_INFO.COINS;
                                BX.selectUtils.addNewOption(BX('coins'), '', 'Выберите монету');
                                for (var sCoin in oCoins) {
                                    BX.selectUtils.addNewOption(BX('coins'), sCoin, sCoin + ' (' + oCoins[sCoin] + ')');
                                }
                                BX.removeClass(BX('coins-wrapper'), 'hidden');
                            }
                        }
                    }
                });
            } else {
                BX('response').innerHTML = '<span class="error">Не указан кошелёк Minter</span>';
            }
        });

        BX.bind(BX('coins'), 'change', function () {
            if (this.value) {
                if (this.value !== 'BIP') {
                    BX('response').innerHTML = '';
                    BX('coin-rate').innerHTML = '';
                    var sSelectedCoin = this.value;
                    BX.ajax({
                        url: arParams['AJAX_PATH'],
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            ACTION: 'getCoinRate',
                            COIN: sSelectedCoin,
                            sessid: BX.bitrix_sessid()
                        },
                        onsuccess: function (response) {
                            if (response.RESULT) {
                                if (response.RESULT.ERRORS) {
                                    BX('response').innerHTML = '<span class="error">' + response.RESULT.ERRORS + '</span>';
                                } else {
                                    BX('coin-rate-wrapper').innerHTML = '1 ' + sSelectedCoin + ' ≈ ' + response.RESULT.COIN_RATE + ' BIP';
                                    BX.removeClass(BX('pay'), 'hidden');
                                    BX.removeClass(BX('coin-rate-wrapper'), 'hidden');
                                }
                            }
                        }
                    });
                } else {
                    BX.removeClass(BX('pay'), 'hidden');
                    BX.removeClass(BX('coin-rate-wrapper'), 'hidden');
                }
            } else {
                BX('coin-rate').innerHTML = '';
                BX.addClass(BX('coin-rate-wrapper'), 'hidden');
                BX.addClass(BX('pay'), 'hidden');
            }
        });
    };
});