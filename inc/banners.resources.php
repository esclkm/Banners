<?php

/**
 * Cotonti Plugin Banners
 * Banner rotation plugin with statistics
 *
 * @package Banners
 * @author Alex
 * @copyright Portal30 2013 http://portal30.ru
 */

$R['banner_image'] = '<img src="{$file}" alt="{$alt}" class="img_hover" style="width:{$width}px; height:{$height}px" />';
$R['banner_image_link'] = '<a href="{$href}" target="_blank" class="rect_infoblock"><span class="widgetimg2">'.$R['banner_image'].'</span></a>';

$R['banner_image_custom'] = $R['banner_image'].'{$customcode}';
$R['banner_image_custom_link'] = '<a href="{$href}" target="_blank" class="rect_infoblock rect_mixed"><span class="widgetimg">'.$R['banner_image'].'</span><span class="widgettext">{$customcode}</span></a>';

$R['banner_flash'] = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="{$width}" height="{$height}" style="display:inline-block;margin:auto;">
        <param name="movie" value="{$file}" />
        <param name="wmode" value="transparent">
        <!--[if !IE]>-->
        <object type="application/x-shockwave-flash" data="{$file}" width="{$width}" height="{$height}">
            <param name="wmode" value="transparent">
        <!--<![endif]-->
        <!--[if !IE]>-->
        </object>
        <!--<![endif]-->
    </object>';
$R['banner_flash_link'] = '<a href="{$href}" target="_blank" style="display:inline-block; width:{$width}px; height:{$height}px; position:relative;">
    <span style="position:absolute; z-index:99; top:0;left:0;width:{$width}px; height:{$height}px;"></span>'.$R['banner_flash'].'</a>';

$R['banner_flash_custom'] = $R['banner_flash'].'<span>{$customcode}</span>';
$R['banner_flash_custom_link'] = $R['banner_flash_link'].'<a href="{$href}" target="_blank" class="rect_infoblock">{$customcode}</a>';

$R['banner_custom'] = '{$customcode}';
$R['banner_custom_link'] = '<a href="{$href}" target="_blank" class="rect_infoblock">{$customcode}</a>';


$R['banner_image_admin'] = $R['banner_image'];
$R['banner_mixed_admin'] = $R['banner_image'];
$R['banner_flash_admin'] = $R['banner_flash'];

$R['banner'] = '{$customcode}';
$R['banner_load'] = '<div style="width: {$width}px; height: {$height}px; line-height: {$height}px; text-align: center; vertical-align: middle; overflow: hidden">
    <img src="/images/spinner.gif">
    </div>';
