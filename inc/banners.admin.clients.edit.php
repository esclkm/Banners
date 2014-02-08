<?php

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

/**
 * Clients Admin Controller class for the Banners
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
	$client = $db->query("SELECT * FROM $db_banner_clients WHERE bac_id = ".(int)$id." LIMIT 1")->fetch();
	$adminpath[] = $L['ba_banner_edit'].": ".htmlspecialchars($client['bac_title']);
}

if ($act == 'save')
{
	$item = array();
	$item['bac_title'] = cot_import('rtitle', 'P', 'TXT');
	if (empty($item['bac_title']))
	{
		cot_error($L['ba_err_titleempty'], 'rtitle');
	}
	$item['bac_purchase_type'] = cot_import('rpurchase_type', 'P', 'INT');
	$item['bac_email'] = cot_import('remail', 'P', 'TXT');
	$item['bac_track_impressions'] = cot_import('rtrack_impressions', 'P', 'INT');
	$item['bac_track_clicks'] = cot_import('rtrack_clicks', 'P', 'INT');
	$item['bac_extrainfo'] = cot_import('rextrainfo', 'P', 'TXT');
	$item['bac_published'] = cot_import('rpublished', 'P', 'BOL');

	if (!cot_error_found())
	{
		if ($id > 0)
		{
			$db->update($db_banner_clients, $item, "bac_id = ".(int)$id);
		}
		else
		{
			$db->insert($db_banner_clients, $item);
			$id = $db->lastInsertId();
		}
		
		cot_message($L['ba_saved']);

		cot_redirect(cot_url('admin', array('m' => 'other', 'p' => 'banners', 'n' => 'clients', 'a' => 'edit', 'id' => $id), '', true));
	}
}

$delUrl = '';
if ($client['bac_id'] > 0)
{
	$delUrl = cot_confirm_url(cot_url('admin', 'm=other&p=banners&n=clients&act=delete&id='.$client['bac_id'].'&'.cot_xg()), 'admin');
}

$track = array(
	-1 => $L['Default'],
	0 => $L['No'],
	1 => $L['Yes']
);

$t->assign(array(
	'FORM_ID' => $client['bac_id'],
	'FORM_TITLE' => cot_inputbox('text', 'rtitle', $client['bac_title'], array('size' => '20', 'maxlength' => '32')),
	'FORM_EMAIL' => cot_inputbox('text', 'remail', $client['bac_email']),
	'FORM_PURCHASE_TYPE' => cot_selectbox($client['bac_purchase_type'], 'rpurchase_type', array_keys($purchase), array_values($purchase), false),
	'FORM_TRACK_IMP' => cot_selectbox($client['bac_track_impressions'], 'rtrack_impressions', array_keys($track), array_values($track), false),
	'FORM_TRACK_CLICKS' => cot_selectbox($client['bac_track_clicks'], 'rtrack_clicks', array_keys($track), array_values($track), false),
	'FORM_EXTRAINFO' => cot_textarea('rextrainfo', $client['bac_extrainfo'], 5, 60),
	'FORM_PUBLISHED' => cot_radiobox(isset($client['published']) ? $client['published'] : 1, 'rpublished', array(1, 0), array($L['Yes'], $L['No'])),
	'FORM_DELETE_URL' => $delUrl,
));

$t->parse('MAIN.FORM');

$t->assign(array(
	'PAGE_TITLE' => isset($client['bac_id']) ? $L['ba_banner_edit'].": ".htmlspecialchars($client['bac_title']) : $L['ba_client_new'],
));

