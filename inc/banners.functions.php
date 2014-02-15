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

// Tables and extras
cot::$db->registerTable('banners');
cot::$db->registerTable('banner_clients');
cot::$db->registerTable('banner_tracks');
cot::$db->registerTable('banner_queries');

cot_extrafields_register_table('banners');

$ba_allowed_ext = array('bmp', 'gif', 'jpg', 'jpeg', 'swf');
$ba_files_dir = 'datas/banners/';
$banner_widget = (isset($banner_widget)) ? $banner_widget : array();
$banner_queries = (isset($banner_queries)) ? $banner_queries : array();
require_once cot_incfile('banners', 'plug', 'resources');

/**
 * Generates a banner widget.
 * Use it as CoTemplate callback.
 *
 * @param string $tpl
 * @param string $cat  Category, semicolon separated
 * @param string $order  'order' OR 'rand'
 * @param int $cnt  Banner count
 * @return string
 *
 */
function banner_widget($cat = '', $cnt = 1, $tpl = 'banners')
{
	global $sys, $cache_ext, $usr, $cfg, $banner_widget;

	// Display the items
	$t = new XTemplate(cot_tplfile($tpl, 'plug'));

	$init = count($banner_widget);
	
	for ($i = $init; $i < $init+$cnt; $i++)
	{

		if (!empty($cache_ext) && $usr['id'] == 0 && $cfg['cache_'.$cache_ext])
		{
			$baclass = 'loading';
		}
		else
		{
			$banner_widget[$i] = $cat;
			$baclass = '';
		}
		
		$t->assign(array(
			'ROW_BANNER' => '{BANNER_POSITION_'.$i.'}',
			'ROW_CAT' => $cat,
			'ROW_LOAD' => $baclass,
		));
		$t->parse('MAIN.ROW');		
	}
	$t->parse('MAIN');
	
	return $t->text('MAIN');
}

function banners_fetch($cat = '', $cnt = 1)
{
	global $db, $cfg, $db_banner_tracks, $sys, $db_banners, $banner_queries;

	$cats = array();
	$cnt = (int)$cnt;
	
	$cond = array(
		'ba_published' => 'ba_published=1',
		'ba_begin' => "ba_begin <='".(int)$sys['now']."'",
		'ba_expire' => "(ba_expire >='".(int)$sys['now']."' OR ba_expire IS NULL OR ba_expire = 0)",
		'ba_imptotal' => "(ba_imptotal = 0 OR ba_impmade < ba_imptotal)"
	);
	

	if((int)$cat > 0 && isset($banner_queries[$cat]))
	{
		$query = $banner_queries[$cat];
		$cat = $query['query_cat'];
		if((int)$query['query_client'])
		{
			$cond['client'] = 'bac_id = '.(int)$query['query_client'];
		}
		if($query['query_string'])
		{
			$bstr = "\$bstr = ".htmlspecialchars_decode($query['query_string']).";";
			eval($bstr);
			$cond['dop'] = $bstr;
		}
		
	}
	
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
	


	if (count($cats) > 0)
	{
		$cond['ba_cat'] = 'ba_cat IN ("'.implode('", "', $cats).'")';
	}

	$ord = ($cfg['plugin']['banners']['bannersort'] == 'random') ? 'RAND()' : "ba_lastimp ASC";
	$cond = array_diff($cond, array(0, null, ''));
	$where = (!empty($cond)) ? ' WHERE '.implode(' AND ', $cond) : '';
	$banners = $db->query("SELECT * FROM $db_banners "
			."$where ORDER BY $ord LIMIT $cnt")->fetchAll();

	return $banners;
}

function banners_load()
{
	global $banner_widget, $banner_queries, $cfg, $db, $db_banner_queries;
	$return_banners = array();
	$banner_id = array();
	if (is_array($banner_widget))
	{
		$counts = array_count_values($banner_widget);
		$queries = array();
		foreach($counts as $val => $count)
		{
			if((int)$val > 0)
			{
				$queries[] = $val;
			}
		}
		if(count($queries))
		{
			$querysql = $db->query("SELECT * FROM $db_banner_queries WHERE query_id IN (".implode(', ', $queries).")");
			while($q = $querysql->fetch())
			{
				$banner_queries[$q['query_id']] = $q;
			}
		}

		foreach ($counts as $val => $count)
		{
			$banners = banners_fetch($val, $count);
			$keys = array_keys($banner_widget, $val);
			$i = 0;
			foreach($banners as $banner)
			{
				$return_banners[$keys[$i]] = banners_image($banner);
				
				if (!(!empty($cache_ext) && $usr['id'] == 0 && $cfg['cache_'.$cache_ext]))
				{
					if ($cfg["plugin"]['banners']['track_impressions'] ||
						($banner['ba_track_impressions'] == 1) ||
						($banner['ba_track_impressions'] == -1 && $banner['bac_track_impressions'] == 1))
					{
						$banner_id[] = $banner['ba_id'];
					}
				}
				$i++;
			}
		}
		
		banner_impress($banner_id, 'impress');
	}
	return $return_banners;
}

function banners_image($banner)
{
	global $cot_extrafields, $db_banners, $L;
	
	$url = cot_url('plug', 'e=banners&a=click&id='.$banner['ba_id']);

	if (!empty($banner['ba_file']))
	{
		$image = false;

		if (in_array($banner['ba_type'], array(TYPE_IMAGE, TYPE_FLASH)))
		{
			$res="banner_";
			$res .= ($banner['ba_type'] == TYPE_IMAGE) ? 'image' : 'flash';
			$res .= (!empty($banner['ba_clickurl'])) ? '_link' : '';
			
			$rc_array = array();
			foreach ($banner as $key => $b)
			{
				$k = preg_replace('/^ba_(.+)/', '$1', $key);
				$rc_array[$k] = $b;
			}

			foreach ($cot_extrafields[$db_banners] as $exfld)
			{
				$rc_array[$exfld['field_name'] . '_title'] = isset($L['ba_' . $exfld['field_name'] . '_title']) ?
					$L['ba_' . $exfld['field_name'] . '_title'] : $exfld['field_description'];
				$rc_array[$exfld['field_name']] = cot_build_extrafields_data('ba_', $exfld, $banner['ba_'.$exfld['field_name']]);
				$rc_array[$exfld['field_name'] . '_value'] = $banner['ba_'.$exfld['field_name']];
			}
			
			$rc_array['href'] = $url;

			$image = cot_rc($res, $rc_array);
		}
	}
	if ($banner['type'] == TYPE_CUSTOM)
	{
		$image = $banner['customcode'];
	}
	return $image;
}

function banner_impress($bannerid, $type = 'impress')
{
	global $db, $db_banner_tracks, $sys, $cfg, $db_banners;

	if (is_array($bannerid))
	{
		$banner_implode = "ba_id IN (".implode(", ", $bannerid).")";
	}
	else
	{
		$banner_implode = "ba_id =".(int)$bannerid."";
		$bannerid = array($bannerid);
	}
	if (count($bannerid))
	{
		if ($type == 'impress')
		{
			$db->query("UPDATE $db_banners SET ba_impmade = ba_impmade+1, ba_lastimp=".(int)$sys['now']." WHERE $banner_implode");
			$track_type = 1;
		}
		else
		{
			$db->query("UPDATE $db_banners SET ba_clicks = ba_clicks+1 WHERE $banner_implode");
			$track_type = 2;
		}

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
				$vals .= "(1, ".(int)$track_type.", ".(int)$bid.", $trackDate)";
			}
		}
		$db->query("INSERT INTO $db_banner_tracks $fields VALUES $vals ON DUPLICATE KEY UPDATE track_count=track_count+1");
	}
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
	global $cfg, $L, $usr, $structure, $cache_ext, $cot_extrafields, $db_banners;

	$temp_array = array();

	if (is_int($banner) && $banner > 0)
	{
		$banner = $db->query("SELECT * FROM $db_banners WHERE ba_id = ".(int)$banner." LIMIT 1")->fetch();
	}
	if ($banner['ba_id'] > 0)
	{
		$temp_array = array(
			'EDIT_URL' => cot_url('admin', array('m' => 'other', 'p' => 'banners', 'a' => 'edit', 'id' => $banner['ba_id'])),
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
			'BANNER' => banners_image($banner)
		);

		foreach ($cot_extrafields[$db_banners] as $exfld)
		{
			$tag = mb_strtoupper($exfld['field_name']);
			$exfld_val = cot_build_extrafields_data('ba_', $exfld, $row['ba_'.$exfld['field_name']]);
			$exfld_title = isset($L['ba_' . $exfld['field_name'] . '_title']) ? $L['ba_' . $exfld['field_name'] . '_title'] : $exfld['field_description'];
			$temp_array[$tag . '_TITLE'] = $exfld_title;
			$temp_array[$tag] = $exfld_val;
			$temp_array[$tag . '_VALUE'] = $row['ba_'.$exfld['field_name']];
		}

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

/**
 * Recalculates banner category counters
 *
 * @param string $cat Cat code
 * @return int
 * @global CotDB $db
 */
function cot_banners_sync($cat)
{
	global $db_banners, $db;
	return $db->query("SELECT COUNT(*) FROM $db_banners WHERE ba_cat ='".$db->query($cat)."'")->fetchColumn();
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
	global $db, $db_banners;
	return (bool)$db->update($db_banners, array("ba_cat" => $newcat), "ba_cat='".$db->prep($oldcat)."'");
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

