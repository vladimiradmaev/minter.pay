$(document).ready(function() {
    $('#minter-pay').click( function(){
        $('#overlay').fadeIn(400,
            function(){
                $('#modal_form')
                    .css('display', 'block')
                    .animate({opacity: 1, top: '45%'}, 200);
            });
    });

    $('#modal_close, #overlay').click( function(){
        $('#modal_form')
            .animate({opacity: 0, top: '45%'}, 200,
                function(){
                    $(this).css('display', 'none');
                    $('#overlay').fadeOut(400);
                }
            );
    });
    $('#cancel').click(function(){
        $('#modal_close').trigger('click');
    });
});