/**
 * Cotonti Plugin Banners
 * Banner rotation plugin with statistics
 *
 * @package Banners
 * @author Alex
 * @copyright Portal30 2013 http://portal30.ru
 */

$(function() {
    var cats = new Array();
    var cnt = 0;
    $('.widget.loading').each(function(){
        var cat = $(this).attr('data-bannerid');
        cnt++;
        cats[cnt] = cat;
        $(this).attr('id', 'swban_'+cnt);
    });

    if(cnt > 0){
        $.post('index.php?e=banners&a=ajax', {cats: cats, x : bannerx}, function(data){
            if(data.error != ''){
                alert(data.error)
            }else{
                $.each(data.banners, function(index, value) {
                    $('#swban_'+index).html(value).removeAttr('id').removeAttr('data-bannerid').removeClass('loading');
                });
            }
        }, 'json');
    }
});