<?php

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

/**
 * Clients Admin Controller class for the Banners
 *
 * @package Banners
 * @author Alex
 * @copyright Portal30 2013 http://portal30.ru
 */
global $L, $adminpath, $cfg, $a, $sys;

$adminpath[] = '&nbsp;'.$L['ba_clients'];

$so = cot_import('so', 'G', 'ALP'); // order field name without 'bac_'
$w = cot_import('w', 'G', 'ALP', 4); // order way (asc, desc)
$act = cot_import('act', 'G', 'ALP');
$maxrowsperpage = $cfg['maxrowsperpage'];
list($pg, $d, $durl) = cot_import_pagenav('d', $maxrowsperpage); //page number for clients list

$list_url_path = array('m' => 'other', 'p' => 'banners', 'n' => 'clients');

if ($act == 'delete')
{
	$urlArr = $list_url_path;
	$urlArr['d'] = $pagenav['current'];
	
	$id = cot_import('id', 'G', 'INT');
	cot_check_xg();

	$item = $db->query("SELECT * FROM $db_banner_clients WHERE bac_id = ".(int)$id." LIMIT 1")->fetch();
	if (!$item)
	{
		cot_error($L['No_items']." id# ".$id);
		cot_redirect(cot_url('admin', $urlArr, '', true));
	}
	$db->delete($db_banner_clients, "bac_id = ".(int)$id);

	cot_message($L['alreadydeletednewentry']." # $id - ".$item['bac_title']);
	cot_redirect(cot_url('admin', $urlArr, '', true));
}


if (empty($so))
{
	$so = 'bac_title';
}
else
{
	$list_url_path['so'] = $so;
}
if (empty($w))
{
	$w = 'ASC';
}
else
{
	$list_url_path['w'] = $w;
}

$res = $db->query("SELECT * FROM $db_banner_clients ORDER BY $so $w LIMIT $d, $maxrowsperpage");
$totallines = $db->query("SELECT COUNT(*) FROM $db_banner_clients")->fetchColumn();;
$list = $res->fetchAll();

$pagenav = cot_pagenav('admin', $list_url_path, $d, $totallines, $maxrowsperpage);

$i = $d + 1;

foreach ($list as $item)
{
	$item_link = cot_url('admin', array('m' => 'other', 'p' => 'banners', 'n' => 'clients', 'a' => 'edit',	'id' => $item['bac_id']));
	$delUrlArr = array_merge($list_url_path, array('act' => 'delete', 'id' => $item['bac_id'], 'd' => $pagenav['current'], 'x' => $sys['xk']));

	$t->assign(array(
		'LIST_ROW_NUM' => $i,
		'LIST_ROW_URL' => $item_link,
		'LIST_ROW_ID' => $item['bac_id'],
		'LIST_ROW_TITLE' => $item['bac_title'],
		'LIST_ROW_PUBLISHED' => $item['bac_published'] ? $L['Yes'] : $L['No'],
		'LIST_ROW_PURCHASE' => $item['bac_purchase_type'],
		'LIST_ROW_PURCHASE_TEXT' => $purchase[$item['bac_purchase_type']],
		'LIST_ROW_DELETE_URL' => cot_confirm_url(cot_url('admin', $delUrlArr), 'admin'),
	));
	$t->parse('MAIN.LIST_ROW');
}
cot_display_messages($t);
$t->assign(array(
	'LIST_PAGINATION' => $pagenav['main'],
	'LIST_PAGEPREV' => $pagenav['prev'],
	'LIST_PAGENEXT' => $pagenav['next'],
	'LIST_CURRENTPAGE' => $pagenav['current'],
	'LIST_TOTALLINES' => $totallines,
	'LIST_MAXPERPAGE' => $maxrowsperpage,
	'LIST_TOTALPAGES' => $pagenav['total'],
	'LIST_ITEMS_ON_PAGE' => $pagenav['onpage'],
	'LIST_URL' => cot_url('admin', $list_url_path, '', true),
	'PAGE_TITLE' => $L['ba_clients'],
));