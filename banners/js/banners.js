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
    $('.banner-loading').each(function(){
        var id = $(this).attr('id');
        id = parseInt(id.replace('banner_', ''));
        var cat = $(this).attr('banner_category');
        if(id > 0){
            cnt++;
            cats[cnt] = cat;
            $(this).attr('id', 'swban_'+cnt);
        }
    });

    if(cnt > 0){
        $.post('index.php?e=banners&a=ajxLoad', {cats: cats, x : bannerx}, function(data){
            if(data.error != ''){
                alert(data.error)
            }else{
                $.each(data.banners, function(index, value) {
                    $('#swban_'+index).html(value);
                });
            }
        }, 'json');
    }
});