<?php

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

/**
 * Main Admin Controller class for the Banners
 *
 * @package Banners
 * @author Alex
 * @copyright Portal30 2013 http://portal30.ru
 */
global $adminpath, $structure, $cfg, $L, $usr, $sys;

$adminpath[] = array(cot_url('admin', array('m' => 'other', 'p' => 'banners')), $L['ba_banners']);

if (empty($structure['banners']))
{
	cot_error($L['ba_category_no']);
}

$id = cot_import('id', 'G', 'INT');

$act = cot_import('act', 'P', 'ALP');
if (!$id)
{
	$id = 0;
	$adminpath[] = '&nbsp;'.$L['Add'];
	$banner = array();
}
else
{
	$banner = $db->query("SELECT * FROM $db_banners WHERE ba_id = ".(int)$id." LIMIT 1")->fetch();
	$adminpath[] = $L['ba_banner_edit'].": ".htmlspecialchars($banner['ba_title']);
}

if ($act == 'save')
{
	$item = array();

	$item['ba_title'] = cot_import('rtitle', 'P', 'TXT');
	if (empty($item['ba_title']))
	{
		cot_error($L['ba_err_titleempty'], 'rtitle');
	}
	$item['ba_cat'] = cot_import('rcat', 'P', 'TXT');
	$file = banners_import_file('rfile', $banner['ba_file']);
	$delFile = cot_import('rdel_rfile', 'P', 'BOL') ? 1 : 0;
	if ($delFile)
	{
		$item['ba_file'] = '';
	}
	$item['ba_type'] = cot_import('rtype', 'P', 'TXT');
	$item['ba_width'] = cot_import('rwidth', 'P', 'INT');
	$item['ba_height'] = cot_import('rheight', 'P', 'INT');

	if (!empty($file))
	{
		// Try to get image size
		@$gd = getimagesize($file);
		if (!$gd)
		{
			cot_error($L['ba_err_inv_file_type'], 'rfile');
		}
		else
		{
			if (empty($item['ba_width']))
			{
				$item['ba_width'] = $gd[0];
			}
			if (empty($item['ba_height']))
			{
				$item['ba_height'] = $gd[1];
			}
			// Get image type
			switch ($gd[2])
			{
				//case 1: // IMAGE
				case IMAGETYPE_GIF:
				case IMAGETYPE_JPEG:
				case IMAGETYPE_PNG:
				case IMAGETYPE_BMP:
					if ($item['ba_type'] != TYPE_CUSTOM)
					{
						$item['ba_type'] = TYPE_IMAGE;
					}
					break;
				//case 4: // SWF ( Flash)
				case IMAGETYPE_SWF:
				case IMAGETYPE_SWC:
					if ($item['ba_type'] != TYPE_CUSTOM)
					{
						$item['ba_type'] = TYPE_FLASH;
					}
					break;
				default:
					cot_error($L['ba_err_inv_file_type'], 'rfile');
			}
		}
	}
	elseif ($item['ba_type'] != TYPE_CUSTOM)
	{
		// Если файл не передан, тип не записываем
		// если честно то тут фигня какаято... надо думать.. а если файл удалнен то какой тип?... в общем тестируем
		if (!$delFile)
		{
			unset($item['ba_type']);
		}
	}

	$item['ba_alt'] = cot_import('ralt', 'P', 'TXT');
	$item['ba_customcode'] = cot_import('rcustomcode', 'P', 'HTM');
	$item['ba_clickurl'] = cot_import('rclickurl', 'P', 'TXT');
	$item['ba_description'] = cot_import('rdescription', 'P', 'TXT');
	$item['ba_sticky'] = cot_import('rsticky', 'P', 'BOL');
	$item['ba_begin'] = cot_import_date('rbegin');
	$item['ba_expire'] = cot_import_date('rexpire');
	$item['ba_imptotal'] = cot_import('rimptotal', 'P', 'INT');
	$item['ba_impmade'] = cot_import('rimpmade', 'P', 'INT');
	$item['ba_clicks'] = cot_import('rclicks', 'P', 'INT');
	$item['bac_id'] = cot_import('rbac_id', 'P', 'INT');
	$item['ba_purchase_type'] = cot_import('rpurchase_type', 'P', 'INT');
	$item['ba_track_impressions'] = cot_import('rtrack_impressions', 'P', 'INT');
	$item['ba_track_clicks'] = cot_import('rtrack_clicks', 'P', 'INT');
	$item['ba_published'] = cot_import('rpublished', 'P', 'BOL');

	$item['ba_created'] = $sys['now'];
	$item['ba_created_by'] = $usr['id'];

	// Extra fields
	foreach ($cot_extrafields[$db_banners] as $exfld)
	{
		$item['ba_' . $exfld['field_name']] = cot_import_extrafields('r' . $exfld['field_name'], $exfld);
	}

	if (!$item['ba_id'])
	{
		// Добавить новую запись
		$item['ba_updated'] = $sys['now'];
		$item['ba_updated_by'] = $usr['id'];
	}	
	
	if (!cot_error_found())
	{
		if (!empty($file))
		{
			$item['ba_file'] = $file;
		}
		if ($id > 0)
		{
			$db->update($db_banners, $item, "ba_id = ".(int)$id);
			cot_log("Edited banner # {$id} - {$data['ba_title']}", 'adm');
		}
		else
		{
			$db->insert($db_banners, $item);
			cot_log("Added new banner # {$id} - {$item['ba_title']}", 'adm');			
		}
		if (!empty($banner['ba_file']) && isset($data['ba_file']) && $banner['ba_file'] != $data['ba_file'] && file_exists($banner['ba_file']))
		{
			unlink($banner['ba_file']);
		}
		cot_extrafield_movefiles();
		cot_message($L['ba_saved']);
		
		cot_redirect(cot_url('admin', array('m' => 'other', 'p' => 'banners', 'a' => 'edit', 'id' => $id), '', true));
	}
	else
	{
		// Удалим загруженный файл
		if (!empty($file) && file_exists($file))
			unlink($file);
		// если честно то фигня вновь... сначала загружаем файл, а потом его удаляем... пересмотреть это!!!!
	}
}
$delUrl = '';
if ($banner['ba_id'] > 0)
{
	$delUrl = cot_confirm_url(cot_url('admin', 'm=other&p=banners&act=delete&id='.$banner['ba_id'].'&'.cot_xg()), 'admin');
}

$types = array(
	'0' => $L['ba_type_file'],
	TYPE_CUSTOM => $L['ba_custom_code']
);

$sql = $db->query("SELECT bac_id, bac_title FROM $db_banner_clients ORDER BY `bac_title` ASC");
$clients = $sql->fetchAll(PDO::FETCH_KEY_PAIR);
$clients = (!$clients) ? array() : $clients;

$track = array(
	-1 => $L['ba_client_default'],
	0 => $L['No'],
	1 => $L['Yes']
);

$formFile = cot_inputbox('file', 'rfile', $banner['ba_file']);
if (!empty($banner['ba_file']))
	$formFile .= cot_checkbox(false, 'rdel_rfile', $L['Delete']);

$t->assign(array(
	'FORM_ID' => $banner['ba_id'],
	'FORM_TITLE' => cot_inputbox('text', 'rtitle', $banner['ba_title'], array('size' => '20',
		'maxlength' => '32')),
	'FORM_CATEGORY' => cot_selectbox_structure('banners', $banner['ba_cat'], 'rcat', '', false, false),
	'FORM_TYPE' => cot_selectbox($banner['ba_type'], 'rtype', array_keys($types), array_values($types), false),
	'FORM_FILE' => $formFile,
	'FORM_WIDTH' => cot_inputbox('text', 'rwidth', $banner['ba_width']),
	'FORM_HEIGHT' => cot_inputbox('text', 'rheight', $banner['ba_height']),
	'FORM_ALT' => cot_inputbox('text', 'ralt', $banner['ba_alt']),
	'FORM_CUSTOMCODE' => cot_textarea('rcustomcode', $banner['ba_customcode'], 5, 60),
	'FORM_CLICKURL' => cot_inputbox('text', 'rclickurl', $banner['ba_clickurl']),
	'FORM_DESCRIPTION' => cot_textarea('rdescription', $banner['ba_description'], 5, 60),
	'FORM_STICKY' => cot_radiobox($banner['ba_sticky'], 'rsticky', array(1, 0), array($L['Yes'], $L['No'])),
	'FORM_BEGIN' => cot_selectbox_date($banner['ba_begin'], 'long', 'rbegin'),
	'FORM_PUBLISH_DOWN' => cot_selectbox_date($banner['ba_expire'], 'long', 'rexpire'),
	'FORM_IMPTOTAL' => cot_inputbox('text', 'rimptotal', $banner['ba_imptotal']),
	'FORM_IMPMADE' => cot_inputbox('text', 'rimpmade', $banner['ba_impmade']),
	'FORM_CLICKS' => cot_inputbox('text', 'rclicks', $banner['ba_clicks']),
	'FORM_CLIENT_ID' => cot_selectbox($banner['bac_id'], 'rbac_id', array_keys($clients), array_values($clients), true),
	'FORM_PURCHASE_TYPE' => cot_selectbox($banner['ba_purchase_type'], 'rpurchase_type', array_keys($purchase), array_values($purchase), false),
	'FORM_TRACK_IMP' => cot_selectbox($banner['ba_track_impressions'], 'rtrack_impressions', array_keys($track), array_values($track), false),
	'FORM_TRACK_CLICKS' => cot_selectbox($banner['ba_track_clicks'], 'rtrack_clicks', array_keys($track), array_values($track), false),
	'FORM_PUBLISHED' => cot_radiobox(isset($banner['ba_published']) ? $banner['ba_published'] : 1, 'rpublished', array(1, 0), array($L['Yes'], $L['No'])),
	'FORM_DELETE_URL' => $delUrl,
));

foreach ($cot_extrafields[$db_banners] as $exfld)
{
	$uname = strtoupper($exfld['field_name']);
	$exfld_val = cot_build_extrafields('r' . $exfld['field_name'], $exfld, $banner['ba_'.$exfld['field_name']]);
	$exfld_title =  isset($L['ba_' . $exfld['field_name'] . '_title']) ? $L['ba_' . $exfld['field_name'] . '_title'] : $exfld['field_description'];
	$t->assign(array(
		'FORM_' . $uname => $exfld_val,
		'FORM_' . $uname . '_TITLE' => $exfld_title,
		'FORM_EXTRAFLD' => $exfld_val,
		'FORM_EXTRAFLD_TITLE' => $exfld_title
		));
	$t->parse('MAIN.FORM.EXTRAFLD');
}

if (!empty($banner['ba_file']))
{	
	// расчитаем размеры картинки:	
	$w = $banner['ba_width'];
	$h = $banner['ba_height'];
	if ($h > 100)
	{
		$k = $w / $h;
		$h = 100;
		$w = intval($h * $k);
	}
	$rc_vars = array(
			'file' => $banner['ba_file'],
			'alt' => $banner['ba_alt'],
			'width' => $w,
			'height' => $h
		);
	if ($banner['ba_type'] == TYPE_IMAGE)
	{
		$image = cot_rc('banner_image', $rc_vars);
		$t->assign('BANNER_IMAGE', $image);
	}
	elseif ($banner['ba_type'] == TYPE_FLASH)
	{
		$image = cot_rc('banner_flash', $rc_vars);
		$t->assign('BANNER_IMAGE', $image);
	}
}


if (!empty($structure['banners']))
{
	$t->parse('MAIN.FORM');
}

$t->assign(array(
	'PAGE_TITLE' => isset($banner['ba_id']) ? $L['ba_banner_edit'].": ".htmlspecialchars($banner['ba_title']) :
		$L['ba_banner_new'],
));