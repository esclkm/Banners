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
$order = 'order';
$cond = array(
	'ba_published' => 'ba_published=1',
	'ba_begin' => "ba_begin <='".(int)$sys['now']."'",
	'ba_expire' => "(ba_expire >='".(int)$sys['now']."' OR ba_expire IS NULL)",
	'ba_imptotal' => "(ba_imptotal = 0 OR ba_impmade < ba_imptotal)"
);

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

	$cond['category'] = 'ba_cat ="' .$db->query($cat).'"';

	if ($client)
	{
		$cond['client'] = 'bac_id = '.(int)$client;
	}

	$ord = 	($order == 'rand') ? 'RAND()' : "ba_lastimp ASC";
	$where = (!empty($cond)) ? 'WHERE '.$cond : '';
	$banner = $db->query("SELECT * FROM $db_ba_banners WHERE $where ORDER BY $ord LIMIT 1")->fetch();
	
	if (empty($banner))
	{
		$ret['banners'][$pid] = '';
		continue;
	}
	
	$banner = $banner[0];
	
	impress_banner($banner);

	$url = cot_url('banners', 'a=click&id='.$banner['ba_id']);
	switch ($banner['ba_type'])
	{

		case TYPE_IMAGE:
			if (!empty($banner['ba_file']))
			{
				$image = cot_rc('banner_image', array(
					'file' => $banner['ba_file'],
					'alt' => $banner['ba_alt'],
					'width' => $banner['ba_width'],
					'height' => $banner['ba_height']
				));
				if (!empty($banner['ba_clickurl']))
				{
					$image = cot_rc_link($url, $image, array('target' => '_blank'));
				}
				$ret['banners'][$pid] = cot_rc('banner', array(
					'banner' => $image
				));
			}
			break;

		case TYPE_FLASH:
			if (!empty($banner['ba_file']))
			{
				$image = cot_rc('banner_flash', array(
					'file' => $banner['ba_file'],
					'width' => $banner['ba_width'],
					'height' => $banner['ba_height']
				));
				if (!empty($banner['ba_clickurl']))
				{
					$image = cot_rc_link($url, $image, array('target' => '_blank'));
				}
				$ret['banners'][$pid] = cot_rc('banner', array(
					'banner' => $image
				));
			}
			break;

		case TYPE_CUSTOM:
			$ret['banners'][$pid] = cot_rc('banner', array(
				'banner' => $banner['ba_customcode']
			));
			break;
	}

	$cnt++;
}


echo json_encode($ret);
exit;
