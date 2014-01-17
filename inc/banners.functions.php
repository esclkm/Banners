<?php

/**
 * Cotonti Plugin Banners
 * Banner rotation plugin with statistics
 *
 * @package Banners
 * @author Alex
 * @copyright Portal30 2013 http://portal30.ru
 */
defined('COT_CODE') or die('Wrong URL.');

// === Типы оплаты ===
$purchase = array(
	-1 => $L['Default'],
	1 => $L['ba_unlimited'],
	2 => $L['ba_pt_yearly'],
	3 => $L['ba_pt_monthly'],
	4 => $L['ba_pt_weekly'],
	5 => $L['ba_pt_daily']
);

// === Типы баннеров ===
define(TYPE_UNKNOWN, 0);
define(TYPE_IMAGE, 1);
define(TYPE_FLASH, 2);
define(TYPE_CUSTOM, 3);

$db_ba_banners = (isset($db_ba_banners)) ? $db_ba_banners : $db_x.'banners';
$db_ba_clients = (isset($db_ba_clients)) ? $db_ba_clients : $db_x.'banner_clients';
$db_ba_tracks = (isset($db_ba_tracks)) ? $db_ba_tracks : $db_x.'banner_tracks';

$ba_allowed_ext = array('bmp', 'gif', 'jpg', 'jpeg', 'swf');
$ba_files_dir = 'datas/banners/';

require_once cot_incfile('banners', 'plug', 'resources');

/**
 * Generates a banner widget.
 * Use it as CoTemplate callback.
 *
 * @param string $tpl
 * @param string $cat  Category, semicolon separated
 * @param string $order  'order' OR 'rand'
 * @param int $cnt  Banner count
 * @param int|bool $client
 * @param int|bool $subcats
 * @return string
 *
 */
function banner_widget($cat = '', $cnt = 1, $tpl = 'banners', $order = 'order', $client = false, $subcats = false)
{
	global $sys, $cache_ext, $usr, $cfg;

	$banners = banners_fetch($cat, $cnt, $client, $order);

	if (!$banners)
		return '';

	// Display the items
	$t = new XTemplate(cot_tplfile($tpl, 'plug'));

	foreach ($banners as $banner)
	{
		// Если включено кеширование и это незарег не засчитываем показ. Баннер будет запрошен аяксом
		if (!(!empty($cache_ext) && $usr['id'] == 0 && $cfg['cache_'.$cache_ext]))
		{
			if ($cfg["plugin"]['banners']['track_impressions'] ||
				($banner['ba_track_impressions'] == 1) ||
				($banner['ba_track_impressions'] == -1 && $banner['bac_track_impressions'] == 1))
			{
				banner_impress($banner['ba_id']);
			}
		}
		$t->assign(banners_generate_tags($banner, 'ROW_'));
		$t->parse('MAIN.ROW');
	}

	$t->parse();
	return $t->text();
}

function banners_fetch($cat = '', $cnt = 1, $client = 0, $order = '')
{
	global $db, $cfg, $db_ba_tracks, $sys, $db_ba_banners;

	$cats = array();
	$client = (int)$client;
	$cnt = (int)$cnt;

	$cat = str_replace(' ', '', $cat);
	$cat = preg_replace('/;;+/', ';', $cat);

	if ($cat != '')
	{
		$categs = explode(';', $cat);
		foreach ($categs as $tmp)
		{
			$tmp = trim($tmp);
			if (empty($tmp))
			{
				continue;
			}
			if ($subcats)
			{
				$cats = array_merge($cats, cot_structure_children('banners', $tmp, true, true, false, false));
			}
			else
			{
				$cats[] = $tmp;
			}
		}
		$cats = array_unique($cats);
	}

	$cond = array(
		'ba_published' => 'ba_published=1',
		'ba_begin' => "ba_begin <='".(int)$sys['now']."'",
		'ba_expire' => "(ba_expire >='".(int)$sys['now']."' OR ba_expire IS NULL OR ba_expire = 0)",
		'ba_imptotal' => "(ba_imptotal = 0 OR ba_impmade < ba_imptotal)"
	);

	if (count($cats) > 0)
	{
		$cond['ba_cat'] = 'ba_cat IN ("'.implode('", "', $cats).'")';
	}

	if ($client)
	{
		$cond['client'] = 'bac_id = '.(int)$client;
	}

	$ord = ($order == 'rand') ? 'RAND()' : "ba_lastimp ASC";

	$where = (!empty($cond)) ? ' WHERE '.implode(' AND ', $cond) : '';
	$banners = $db->query("SELECT * FROM $db_ba_banners "
			."$where ORDER BY $ord LIMIT $cnt")->fetchAll();

	return $banners;
}

function banner_impress($bannerid, $type = 'impress')
{
	global $db, $db_ba_tracks, $sys, $cfg, $db_ba_banners;

	if (is_array($bannerid))
	{
		$banner_implode = "ba_id IN (".implode(", ", $bannerid).")";
	}
	else
	{
		$banner_implode = "ba_id =".(int)$bannerid."";
		$bannerid = array($bannerid);
	}

	$db->query("UPDATE $db_ba_banners SET ba_impmade = ba_impmade+1, ba_lastimp=".(int)$sys['now']." WHERE $banner_implode");

	$trackDate = cot_stamp2date(date('Y-m-d H', $sys['now']).':00:00');
	$fields = '(track_count, track_type, ba_id, track_date)';
	$vals = '';
	foreach ($bannerid as $bid)
	{
		if ((int)$bid > 0)
		{
			if (!empty($vals))
			{
				$vals .= ', ';
			}
			$vals = "(1, 1, ".(int)$bid.", $trackDate)";
		}
	}
	$db->query("INSERT INTO $db_ba_tracks $fields VALUES $fields ON DUPLICATE KEY UPDATE track_count=track_count+1");
}

/**
 * Импортировать файл
 */
function banners_import_file($inputname, $oldvalue = '')
{
	global $lang, $L, $cot_translit, $ba_allowed_ext, $ba_files_dir, $cfg;

	$import = !empty($_FILES[$inputname]) ? $_FILES[$inputname] : array();
	$import['delete'] = cot_import('rdel_'.$inputname, 'P', 'BOL') ? 1 : 0;

	// Если пришел файл или надо удалить существующий
	if (is_array($import) && !$import['error'] && !empty($import['name']))
	{
		$fname = mb_substr($import['name'], 0, mb_strrpos($import['name'], '.'));
		$ext = mb_strtolower(mb_substr($import['name'], mb_strrpos($import['name'], '.') + 1));

		if (!file_exists($ba_files_dir))
		{
			mkdir($ba_files_dir);
		}
		//check extension
		if (empty($ba_allowed_ext) || in_array($ext, $ba_allowed_ext))
		{
			if ($lang != 'en')
			{
				require_once cot_langfile('translit', 'core');
				$fname = (is_array($cot_translit)) ? strtr($fname, $cot_translit) : '';
			}
			$fname = str_replace(' ', '_', $fname);
			$fname = preg_replace('#[^a-zA-Z0-9\-_\.\ \+]#', '', $fname);
			$fname = str_replace('..', '.', $fname);
			$fname = (empty($fname)) ? cot_unique() : $fname;

			$fname .= (file_exists("{$ba_files_dir}/$fname.$ext") && $oldvalue != $fname.'.'.$ext) ? date("YmjGis") : '';
			$fname .= '.'.$ext;

			$file['old'] = (!empty($oldvalue) && ($import['delete'] || $import['tmp_name'])) ? $oldvalue : '';
			$file['tmp'] = (!$import['delete']) ? $import['tmp_name'] : '';
			$file['new'] = (!$import['delete']) ? $ba_files_dir.$fname : '';

			if (!empty($file['old']) && file_exists($file['old']))
			{
				unlink($file['old']);
			}
			if (!empty($file['tmp']) && !empty($file['tmp']))
			{
				move_uploaded_file($file['tmp'], $file['new']);
			}

			return $file['new'];
		}
		else
		{
			cot_error($L['ba_err_inv_file_type'], $inputname);
			return '';
		}
	}
}

/**
 * Recalculates banner category counters
 *
 * @param string $cat Cat code
 * @return int
 * @global CotDB $db
 */
function cot_banners_sync($cat)
{
	global $db_ba_banners, $db;
	return $db->query("SELECT COUNT(*) FROM $db_ba_banners WHERE ba_cat ='".$db->query($cat)."'")->fetchColumn();
}

/**
 * Update banner category code
 *
 * @param string $oldcat Old Cat code
 * @param string $newcat New Cat code
 * @return bool
 * @global CotDB $db
 */
function cot_banners_updatecat($oldcat, $newcat)
{
	global $db, $db_ba_banners;
	return (bool)$db->update($db_ba_banners, array("ba_cat" => $newcat), "ba_cat='".$db->prep($oldcat)."'");
}

/**
 * Renders stucture dropdown
 *
 * @param string $check Seleced value
 * @param string $name Dropdown name
 * @param bool $add_empty
 * @return string
 * @global CotDB $db
 */
function banners_selectbox($check, $name, $add_empty = false)
{
	global $structure;
	$structure['banners'] = (is_array($structure['banners'])) ? $structure['banners'] : array();
	$result_array = array();
	foreach ($structure['banners'] as $i => $x)
	{
		if ($i != 'all')
		{
			$result_array[$i] = $x['tpath'];
		}
	}
	$result = cot_selectbox($check, $name, array_keys($result_array), array_values($result_array), $add_empty);

	return($result);
}

/**
 * Remove dir
 * @param $path
 */
function banners_remove_dir($path)
{
	if (file_exists($path) && is_dir($path))
	{
		$dirHandle = opendir($path);
		while (false !== ($file = readdir($dirHandle)))
		{
			if ($file != '.' && $file != '..')
			{// исключаем папки с назварием '.' и '..'
				$tmpPath = $path.'/'.$file;
				chmod($tmpPath, 0777);

				// если папка
				if (is_dir($tmpPath))
				{
					RemoveDir($tmpPath);
				}
				else
				{
					// удаляем файл
					if (file_exists($tmpPath))
						unlink($tmpPath);
				}
			}
		}
		closedir($dirHandle);

		// удаляем текущую папку
		if (file_exists($path))
			rmdir($path);
	}else
	{
		echo "Deleting directory not exists or it's file!";
	}
}

// === Методы для работы с шаблонами ===
/**
 * Returns banner tags for coTemplate
 *
 * @param array|int $banner Banner array or ID
 * @param string $tagPrefix Prefix for tags
 * @return array|void
 */
function banners_generate_tags($banner, $tagPrefix = '')
{
	global $cfg, $L, $usr, $structure, $cache_ext;

	static $extp_first = null, $extp_main = null;

	$temp_array = array();
	if (is_null($extp_first))
	{
		$extp_first = cot_getextplugins('banners.tags.first');
		$extp_main = cot_getextplugins('banners.tags.main');
	}

	/* === Hook === */
	foreach ($extp_first as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (is_int($banner) && $banner > 0)
	{
		$banner = $db->query("SELECT * FROM $db_ba_banners WHERE ba_id = ".(int)$banner." LIMIT 1")->fetch();
	}
	if ($banner['ba_id'] > 0)
	{
		$item_link = cot_url('admin', array('m' => 'other', 'p' => 'banners', 'a' => 'edit', 'id' => $banner['ba_id']));

		$temp_array = array(
			'EDIT_URL' => $item_link,
			'URL' => $banner['ba_clickurl'],
			'ID' => $banner['ba_id'],
			'TITLE' => htmlspecialchars($banner['ba_title']),
			'STICKY' => $banner['ba_sticky'],
			'STICKY_TEXT' => $banner['ba_sticky'] ? $L['Yes'] : $L['No'],
			'CLIENT_TITLE' => htmlspecialchars($banner['bac_title']),
			'IMPTOTAL' => $banner['ba_imptotal'],
			'IMPTOTAL_TEXT' => ($banner['ba_imptotal'] > 0) ? $banner['ba_imptotal'] : $L['ba_unlimited'],
			'IMPMADE' => $banner['ba_impmade'],
			'CLICKS' => $banner['ba_clicks'],
			'CATEGORY' => $banner['ba_cat'],
			'CATEGORY_TITLE' => htmlspecialchars($structure['ba_banners'][$banner['ba_cat']]['title']),
			'CLICKS_PERSENT' => ($banner['ba_impmade'] > 0) ?
				round($banner['ba_clicks'] / $banner['ba_impmade'] * 100, 0)." %" : '0 %',
			'WIDTH' => $banner['ba_width'],
			'HEIGHT' => $banner['ba_height'],
			'TYPE' => $banner['ba_type'],
			'PUBLISHED' => $banner['ba_published'] ? $L['Yes'] : $L['No'],
			'CLASS' => '',
			'CACHE' => 0
		);

		if (!empty($cache_ext) && $usr['id'] == 0 && $cfg['cache_'.$cache_ext])
		{
			// учесть кеширование - запрашивать баннер аяксом
			$temp_array['CLASS'] = 'banner-loading';
			$temp_array['CACHE'] = 1;
			$image = cot_rc('banner_load', array(
				'width' => $banner['ba_width'],
				'height' => $banner['ba_height']
			));
			$temp_array['BANNER'] = $image;
		}
		else
		{
			$temp_array['BANNER'] = banners_image($banner);	
		}
		
		/* === Hook === */
		foreach ($extp_main as $pl)
		{
			include $pl;
		}
		/* ===== */
	}
	else
	{
		// Диалога не существует
	}

	$return_array = array();
	foreach ($temp_array as $key => $val)
	{
		$return_array[$tagPrefix.$key] = $val;
	}

	return $return_array;
}

function banners_image($banner)
{
	$url = cot_url('plug', 'e=banners&a=click&id='.$banner['ba_id']);

	if (!empty($banner['ba_file']))
	{
		$image = false;
		if ($banner['ba_type'] == TYPE_IMAGE)
		{
			// расчитаем размеры картинки:
			$w = $banner['ba_width'];
			$h = $banner['ba_height'];
			$image = cot_rc('banner_image', array(
				'file' => $banner['ba_file'],
				'alt' => $banner['ba_alt'],
				'width' => $w,
				'height' => $h
			));
		}
		elseif ($banner['ba_type'] == TYPE_FLASH)
		{
			$w = $banner['ba_width'];
			$h = $banner['ba_height'];
			$image = cot_rc('banner_flash', array(
				'file' => $banner['ba_file'],
				'width' => $w,
				'height' => $h
			));
		}
		if (!empty($image) && !empty($banner['ba_clickurl']))
		{
			$image = cot_rc_link($url, $image, array('target' => '_blank'));
		}
	}
	if ($banner['type'] == TYPE_CUSTOM)
	{
		$image = $banner['customcode'];
	}
	return $image;
}
