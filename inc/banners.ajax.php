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

$ret = array(
	'error' => ''
);

$cats = cot_import('cats', 'P', 'ARR');
//     cot_watch($cats);
if (!$cats)
{
	$ret['error'] = 'Nothing to load';
	echo json_encode($ret);
	exit;
}

// Пока выбыраем баненры по одному,
// @todo оптимизировать
// @todo учесть $client, $order
$client = false;
$order = 'rand';

$cnt = 0;
foreach ($cats as $pid => $cat)
{
	$pid = (int)$pid;
	$cat = cot_import($cat, 'D', 'TXT');

	if ($pid == 0)
		continue;
	if (empty($cat))
	{
		$ret['banners'][$pid] = '';
		continue;
	}
	$banner = banners_fetch($cat, 1, $client, $order);
	$banner = $banner[0];
	if (empty($banner))
	{
		$ret['banners'][$pid] = '';
		continue;
	}
	
	if ($cfg["plugin"]['banners']['track_impressions'] ||
		($banner['ba_track_impressions'] == 1) ||
		($banner['ba_track_impressions'] == -1 && $banner['bac_track_impressions'] == 1))
	{
		banner_impress($banner['ba_id']);
	}
	$ret['banners'][$pid] = banners_image($banner);

	$cnt++;
}


echo json_encode($ret);
exit;
