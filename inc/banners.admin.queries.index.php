<?php

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

/**
 * queries Admin Controller class for the Banners
 *
 * @package Banners
 * @author Alex
 * @copyright Portal30 2013 http://portal30.ru
 */
global $L, $adminpath, $cfg, $a, $sys;

$adminpath[] = '&nbsp;'.$L['ba_queries'];

$act = cot_import('act', 'G', 'ALP');
$maxrowsperpage = $cfg['maxrowsperpage'];
list($pg, $d, $durl) = cot_import_pagenav('d', $maxrowsperpage); //page number for queries list

$list_url_path = array('m' => 'other', 'p' => 'banners', 'n' => 'queries');

if ($act == 'delete')
{
	$urlArr = $list_url_path;
	$urlArr['d'] = $pagenav['current'];
	
	$id = cot_import('id', 'G', 'INT');
	cot_check_xg();

	$item = $db->query("SELECT * FROM $db_banner_queries WHERE query_id = ".(int)$id." LIMIT 1")->fetch();
	if (!$item)
	{
		cot_error($L['No_items']." id# ".$id);
		cot_redirect(cot_url('admin', $urlArr, '', true));
	}
	$db->delete($db_banner_queries, "query_id = ".(int)$id);

	cot_message($L['alreadydeletednewentry']." # $id - ".$item['query_id']);
	cot_redirect(cot_url('admin', $urlArr, '', true));
}


$res = $db->query("SELECT * FROM $db_banner_queries ORDER BY query_id ASC LIMIT $d, $maxrowsperpage");
$totallines = $db->query("SELECT COUNT(*) FROM $db_banner_queries")->fetchColumn();;
$list = $res->fetchAll();

$pagenav = cot_pagenav('admin', $list_url_path, $d, $totallines, $maxrowsperpage);

$i = $d + 1;

foreach ($list as $item)
{
	$item_link = cot_url('admin', array('m' => 'other', 'p' => 'banners', 'n' => 'queries', 'a' => 'edit',	'id' => $item['query_id']));
	$delUrlArr = array_merge($list_url_path, array('act' => 'delete', 'id' => $item['query_id'], 'd' => $pagenav['current'], 'x' => $sys['xk']));

	$t->assign(array(
		'LIST_ROW_NUM' => $i,
		'LIST_ROW_URL' => $item_link,
		'LIST_ROW_ID' => $item['query_id'],
		'LIST_ROW_CAT' => $item['query_cat'],
		'LIST_ROW_CLIENT' => $item['query_client'],
		'LIST_ROW_STRING' => $item['query_string'],
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
	'PAGE_TITLE' => $L['ba_queries'],
));