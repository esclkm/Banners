<?php

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

/**
 * Main Admin Controller class for the Banners
 *
 * @package Banners
 * @author Alex
 * @copyright Portal30 2013 http://portal30.ru
 */
global $L, $adminpath, $cfg, $sys;

$adminpath[] = '&nbsp;'.$L['ba_banners'];

$sortFields = array(
	'ba_id' => 'ID',
	'ba_title' => $L['Title'],
	'ba_cat' => $L['Category'],
	'ba_published' => $L['ba_published'],
	'bac_id' => $L['ba_client'],
	'ba_impmade' => $L['ba_impressions'],
	'ba_clicks' => $L['ba_clicks'],
	'ba_begin' => $L['ba_begin'],
	'ba_expire' => $L['ba_expire']
);

$so = cot_import('so', 'G', 'ALP'); // order field name without 'ba_'
$w = cot_import('w', 'G', 'ALP', 4); // order way (asc, desc)
$fil = cot_import('fil', 'G', 'ARR');  // filters

$maxrowsperpage = $cfg['maxrowsperpage'];
list($pg, $d, $durl) = cot_import_pagenav('d', $maxrowsperpage); //page number for banners list

$list_url_path = array('m' => 'other', 'p' => 'banners');
if (empty($so))
{
	$so = 'ba_title';
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
if ($so == 'bac_id')
{
	$so = 'b.bac_id';
}

$cond = array();

if (!empty($fil))
{
	foreach ($fil as $key => $val)
	{
		$val = trim(cot_import($val, 'D', 'TXT'));
		if (empty($val) && $val !== '0')
			continue;
		if (in_array($key, array('title')))
		{
			$cond[$key] = "ba_" . $key . " LIKE '%" . $val . "%'";
			$list_url_path["fil[{$key}]"] = $val;
		}
		elseif ($key == 'bac_id')
		{
			$cond[$key] = 'b.bac_id = '.$val;
			$list_url_path["fil[{$key}]"] = $val;
		}
		else
		{
			$cond[$key] = "ba_" . $key . "='" . $db->prep($val)."'";
			$list_url_path["fil[{$key}]"] = $val;
		}
	}
}
else
{
	$fil = array();
}

$act = cot_import('act', 'G', 'ALP');
if ($act == 'delete')
{
	$urlArr = $list_url_path;
	if ($pagenav['current'] > 0)
		$urlArr['d'] = $pagenav['current'];
	$id = cot_import('id', 'G', 'INT');
	cot_check_xg();
	
	$item = $db->query("SELECT * FROM $db_banners WHERE ba_id = ".(int)$id." LIMIT 1")->fetch();
	if (!$item)
	{
		cot_error($L['No_items']." id# ".$id);
		cot_redirect(cot_url('admin', $urlArr, '', true));
	}
	
	$db->delete($db_banners, "ba_id = ".(int)$id);
	$db->delete($db_banner_tracks, "ba_id = ".(int)$id);
	if (file_exists($item['ba_file']))
		unlink($item['ba_file']);

	foreach ($cot_extrafields[$db_banners] as $exfld)
	{
		cot_extrafield_unlinkfiles($item['ba_' . $exfld['field_name']], $exfld);
	}
	cot_message($L['alreadydeletednewentry']." # $id - {$item['ba_title']}");
	cot_redirect(cot_url('admin', $urlArr, '', true));
}


$cond = implode(' AND ', $cond);
$cond = (!empty($cond)) ? 'WHERE '.$cond : '';

$res = $db->query("SELECT b.*, c.* FROM $db_banners AS b LEFT JOIN $db_banner_clients as c  ON b.bac_id=c.bac_id $cond ORDER BY $so $w LIMIT $d, $maxrowsperpage");
$totallines = $db->query("SELECT COUNT(*) FROM $db_banners AS b $cond")->fetchColumn();
$list = $res->fetchAll();

$pagenav = cot_pagenav('admin', $list_url_path, $d, $totallines, $maxrowsperpage);

$i = $d + 1;
foreach ($list as $item)
{
	$t->assign(banners_generate_tags($item, 'LIST_ROW_'));
	
	$delUrlArr = array_merge($list_url_path, array('act' => 'delete',	'id' => $item['ba_id'], 'd' => $pagenav['current'],'x' => $sys['xk']));
	$t->assign(array(
		'LIST_ROW_NUM' => $i,
		'LIST_ROW_DELETE_URL' => cot_confirm_url(cot_url('admin', $delUrlArr), 'admin'),
	));
	$i++;
	$t->parse('MAIN.LIST_ROW');
}

$sql = $db->query("SELECT bac_id, bac_title FROM $db_banner_clients ORDER BY `bac_title` ASC");
$clients = $sql->fetchAll(PDO::FETCH_KEY_PAIR);
$clients = (!$clients) ? array() : $clients;

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
	'SORT_BY' => cot_selectbox($so, 'so', array_keys($sortFields), array_values($sortFields), false),
	'SORT_WAY' => cot_selectbox($w, 'w', array('ASC', 'DESC'), array($L['Ascending'], $L['Descending']), false),
	'FILTER_PUBLISHED' => cot_selectbox($fil['published'], 'fil[published]', array(0, 1), array($L['No'], $L['Yes'])),
	'FILTER_CLIENT' => cot_selectbox($fil['bac_id'], 'fil[bac_id]', array_keys($clients), array_values($clients)),
	'FILTER_CATEGORY' => banners_selectbox($fil['cat'], 'fil[cat]', true),
	'FILTER_VALUES' => $fil
));

$t->assign(array(
	'PAGE_TITLE' => $L['ba_banners'],
));