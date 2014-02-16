<?php

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

/**
 * queries Admin Controller class for the Banners
 *
 * @package Banners
 * @author Alex
 * @copyright Portal30 2013 http://portal30.ru
 */
global $adminpath, $structure, $cfg, $L, $usr, $sys;

$adminpath[] = array(cot_url('admin', array('m' => 'other', 'p' => 'banners')), $L['ba_banners']);

$id = cot_import('id', 'G', 'INT');

$act = cot_import('act', 'P', 'ALP');
if (!$id)
{
	$id = 0;
	$adminpath[] = '&nbsp;'.$L['Add'];
	$client = array();
}
else
{
	$client = $db->query("SELECT * FROM $db_banner_queries WHERE query_id = ".(int)$id." LIMIT 1")->fetch();
	$adminpath[] = $L['ba_query_edit'].": ".htmlspecialchars($client['query_id']);
}

if ($act == 'save')
{
	$item = array();
	$item['query_cat'] = cot_import('rcat', 'P', 'TXT');
	$item['query_client'] = cot_import('rclient', 'P', 'INT');
	$item['query_string'] = cot_import('rstring', 'P', 'TXT');

	if (!cot_error_found())
	{
		if ($id > 0)
		{
			$db->update($db_banner_queries, $item, "query_id = ".(int)$id);
		}
		else
		{
			$db->insert($db_banner_queries, $item);
			$id = $db->lastInsertId();
		}
		
		cot_message($L['ba_saved']);

		cot_redirect(cot_url('admin', array('m' => 'other', 'p' => 'banners', 'n' => 'queries'), '', true));
	}
}

$delUrl = '';
if ($client['query_id'] > 0)
{
	$delUrl = cot_confirm_url(cot_url('admin', 'm=other&p=banners&n=queries&act=delete&id='.$client['query_id'].'&'.cot_xg()), 'admin');
}


$t->assign(array(
	'FORM_ID' => $client['query_id'],
	'FORM_CAT' => cot_inputbox('text', 'rcat', $client['query_cat']),
	'FORM_CLIENT' => cot_inputbox('text', 'rclient', $client['query_client']),
	'FORM_STRING' => cot_inputbox('text', 'rstring', $client['query_string']),
	'FORM_DELETE_URL' => $delUrl,
));

cot_display_messages($t);

$t->assign(array(
	'PAGE_TITLE' => isset($client['query_id']) ? $L['ba_query_edit'].": ".htmlspecialchars($client['query_id']) : $L['ba_query_new'],
));

