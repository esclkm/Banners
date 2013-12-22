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
define(PURCHASE_DEFAULT, -1);
define(PURCHASE_UNLIMITED, 1);
define(PURCHASE_YEARLY, 2);
define(PURCHASE_MONTHLY, 3);
define(PURCHASE_WEEKLY, 4);
define(PURCHASE_DAILY, 5);

$purchase = array(
	PURCHASE_DEFAULT => $L['Default'],
	PURCHASE_UNLIMITED => $L['ba_unlimited'],
	PURCHASE_YEARLY => $L['ba_pt_yearly'],
	PURCHASE_MONTHLY => $L['ba_pt_monthly'],
	PURCHASE_WEEKLY => $L['ba_pt_weekly'],
	PURCHASE_DAILY => $L['ba_pt_daily']
);

// === Типы баннеров ===
define(TYPE_UNKNOWN, 0);
define(TYPE_IMAGE, 1);
define(TYPE_FLASH, 2);
define(TYPE_CUSTOM, 3);

$db_ba_banners = (isset($db_ba_bannerse)) ? $db_ba_banners : $db_x.'banners';
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

	$cats = array();
	$client = (int)$client;
	$cnt = (int)$cnt;

	if ($cat != '')
	{
		$categs = explode(';', $cat);
		if (is_array($categs))
		{
			foreach ($categs as $tmp)
			{
				$tmp = trim($tmp);
				if (empty($tmp))
				{
					continue;
				}
				if ($subcats)
				{
					// Specific cat
//                    var_dump(cot_structure_children('banners', $tmp));
					$cats = array_merge($cats, cot_structure_children('banners', $tmp, true, true, false, false));
				}
				else
				{
					$cats[] = $tmp;
				}
			}
		}
		$cats = array_unique($cats);
	}

	$cond = array(
		'ba_published' => 'ba_published=1',
		'ba_begin' => "ba_begin <='".(int)$sys['now']."'",
		'ba_expire' => "(ba_expire >='".(int)$sys['now']."' OR ba_expire IS NULL)",
		'ba_imptotal' => "(ba_imptotal = 0 OR ba_impmade < ba_imptotal)"
	);
	if (count($cats) > 0)
	{
		$cond['ba_cat'] = 'ba_cat IN ("'.implode('", "', $cats).'"';
	}
	if ($client)
	{
		$cond['client'] = 'bac_id = '.(int)$client;
	}
	$ord = 	($order == 'rand') ? 'RAND()' : "ba_lastimp ASC";
	
	$where = (!empty($cond)) ? 'WHERE '.$cond : '';
	$banners = $db->query("SELECT * FROM $db_ba_banners WHERE $where ORDER BY $ord LIMIT $cnt")->fetchAll();
	
	if (!$banners)
		return '';

	// Display the items
	$t = new XTemplate(cot_tplfile($tpl, 'plug'));

	foreach ($banners as $banner)
	{
		// Если включено кеширование и это незарег не засчитываем показ. Баннер будет запрошен аяксом
		if (!(!empty($cache_ext) && $usr['id'] == 0 && $cfg['cache_'.$cache_ext]))
		{
			impress_banner($banner);
		}
		$t->assign(banner_generateTags($banner, 'ROW_'));
		$t->parse('MAIN.ROW');
	}

	$t->parse();
	return $t->text();
}

function impress_banner($banner)
{
	global $db, $db_ba_tracks, $sys, $cfg, $db_ba_banners;

	$db->update("UPDATE $db_ba_banners SET ba_impmade = ba_impmade+1, ba_lastimp=".(int)$sys['now']." WHERE ba_id = ".(int)$banner['ba_id']." LIMIT 1");

	if ($cfg["plugin"]['banners']['track_impressions'] ||
		($banner['ba_track_impressions'] == 1) ||
		($banner['ba_track_impressions'] == -1 && $banner['bac_track_impressions'] == 1))
	{
		$trackDate = cot_stamp2date(date('Y-m-d H', $sys['now']).':00:00');

		$sql = "SELECT `track_count` FROM $db_ba_tracks
                WHERE track_type=1 AND ba_id={$banner['ba_id']} AND track_date='{$trackDate}'";

		$count = $db->query($sql)->fetchColumn();

		if ($count)
		{
			// update count
			$data = array('track_count' => $count + 1);
			$db->update($db_ba_tracks, $data, "track_type=1 AND ba_id={$banner['ba_id']} AND track_date='{$trackDate}'");
		}
		else
		{
			// insert new count
			$data = array(
				'track_count' => 1,
				'track_type' => 1,
				'ba_id' => (int)$banner['ba_id'],
				'track_date' => $trackDate
			);
			$db->insert($db_ba_tracks, $data);
		}
	}
}

/**
 * Импортировать файл
 */
function ba_importFile($inputname, $oldvalue = '')
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
	return $db->query("SELECT COUNT(*) FROM $db_ba_banners WHERE ba_cat ='" .$db->query($cat)."'")->fetchColumn();
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
 * @param string $extension Extension code
 * @param string $check Seleced value
 * @param string $name Dropdown name
 * @param string $subcat Show only subcats of selected category
 * @param bool $hideprivate Hide private categories
 * @param bool $is_module TRUE for modules, FALSE for plugins
 * @param bool $add_empty
 * @return string
 * @global CotDB $db
 */
function ba_selectbox_structure($extension, $check, $name, $subcat = '', $hideprivate = true, $is_module = true, $add_empty = false)
{
	global $structure;

	$structure[$extension] = (is_array($structure[$extension])) ? $structure[$extension] : array();

	$result_array = array();
	foreach ($structure[$extension] as $i => $x)
	{
		$display = ($hideprivate && $is_module) ? cot_auth($extension, $i, 'W') : true;
		if ($display && !empty($subcat) && isset($structure[$extension][$subcat]))
		{
			$mtch = $structure[$extension][$subcat]['path'].".";
			$mtchlen = mb_strlen($mtch);
			$display = (mb_substr($x['path'], 0, $mtchlen) == $mtch || $i === $subcat);
		}

		if ((!$is_module || cot_auth($extension, $i, 'R')) && $i != 'all' && $display)
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
function ba_removeDir($path)
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

/**
 * Files list in folder
 * @param $folder
 * @return array
 */
function ba_getFilesList($folder)
{
	$all_files = array();
	$fp = opendir($folder);
	while ($cv_file = readdir($fp))
	{
		if (is_file($folder."/".$cv_file))
		{
			$all_files[] = $folder."/".$cv_file;
		}
		elseif ($cv_file != "." && $cv_file != ".." && is_dir($folder."/".$cv_file))
		{
			GetListFiles($folder."/".$cv_file, $all_files);
		}
	}
	closedir($fp);
	return $all_files;
}

// === Методы для работы с шаблонами ===
/**
 * Returns banner tags for coTemplate
 *
 * @param array|int $banner Banner array or ID
 * @param string $tagPrefix Prefix for tags
 * @return array|void
 */
function banner_generateTags($banner, $tagPrefix = '')
{
	global $cfg, $L, $usr, $structure, $cache_ext;

	static $extp_first = null, $extp_main = null;

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
	if ($banner['id'] > 0)
	{
		$item_link = cot_url('admin', array('m' => 'other', 'p' => 'banners', 'a' => 'edit', 'id' => $banner['id']));

		$temp_array = array(
			'EDIT_URL' => $item_link,
			'URL' => $banner['clickurl'],
			'ID' => $banner['id'],
			'TITLE' => htmlspecialchars($banner['title']),
			'STICKY' => $banner['sticky'],
			'STICKY_TEXT' => $banner['sticky'] ? $L['Yes'] : $L['No'],
			'CLIENT_TITLE' => htmlspecialchars($banner['bac_title']),
			'IMPTOTAL' => $banner['imptotal'],
			'IMPTOTAL_TEXT' => ($banner['imptotal'] > 0) ? $banner['imptotal'] : $L['ba_unlimited'],
			'IMPMADE' => $banner['impmade'],
			'CLICKS' => $banner['clicks'],
			'CATEGORY' => $banner['cat'],
			'CATEGORY_TITLE' => htmlspecialchars($structure['banners'][$banner['cat']]['title']),
			'CLICKS_PERSENT' => ($banner['impmade'] > 0) ?
				round($banner['clicks'] / $banner['impmade'] * 100, 0)." %" : '0 %',
			'WIDTH' => $banner['width'],
			'HEIGHT' => $banner['height'],
			'TYPE' => $banner['type'],
			'PUBLISHED' => $banner['published'] ? $L['Yes'] : $L['No'],
			'CLASS' => '',
			'CACHE' => 0
		);

		if (!empty($cache_ext) && $usr['id'] == 0 && $cfg['cache_'.$cache_ext])
		{
			// учесть кеширование - запрашивать баннер аяксом
			$temp_array['CLASS'] = 'banner-loading';
			$temp_array['CACHE'] = 1;
			$image = cot_rc('banner_load', array(
				'width' => $banner['width'],
				'height' => $banner['height']
			));
			$temp_array['BANNER'] = cot_rc('banner', array(
				'banner' => $image
			));
		}
		else
		{
			// Вывод обычным образом
			$url = cot_url('plug', 'e=banners&a=click&id='.$banner['id']);

			if (!empty($banner['file']))
			{
				$image = false;
				if ($banner['type'] == TYPE_IMAGE)
				{
					// расчитаем размеры картинки:
					$w = $banner['width'];
					$h = $banner['height'];
					$image = cot_rc('banner_image', array(
						'file' => $banner['file'],
						'alt' => $banner['alt'],
						'width' => $w,
						'height' => $h
					));
				}
				elseif ($banner['type'] == TYPE_FLASH)
				{
					$w = $banner['width'];
					$h = $banner['height'];
					$image = cot_rc('banner_flash', array(
						'file' => $banner['file'],
						'width' => $w,
						'height' => $h
					));
				}
				if (!empty($image))
				{
					if (!empty($banner['clickurl']))
					{
						$image = cot_rc_link($url, $image, array('target' => '_blank'));
					}
					$temp_array['BANNER'] = cot_rc('banner', array(
						'banner' => $image
					));
				}
			}
			if ($banner['type'] == TYPE_CUSTOM)
			{
				$temp_array['BANNER'] = cot_rc('banner', array(
					'banner' => $banner['customcode']
				));
			}
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
