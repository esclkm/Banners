<?php

defined('COT_CODE') or die('Wrong URL.');

/**
 * Main Controller class for the Banners
 *
 * @package Banners
 * @author Alex
 * @copyright Portal30 2013 http://portal30.ru
 */
global $sys;

$banner_widget = cot_import('cats', 'P', 'ARR');
//     cot_watch($cats);
if (!$banner_widget)
{
	$ret['error'] = 'Nothing to load';
	echo json_encode($ret);
	exit;
}

$banners = banners_load();

echo json_encode($banners);
exit;
