<?php

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');


global $L, $cfg, $db_ba_banners, $db_ba_clients, $db_ba_tracks, $db;

$so = cot_import('so', 'G', 'ALP'); // order field name without 'ba_'
$w = cot_import('w', 'G', 'ALP', 4); // order way (asc, desc)

$fil = cot_import('fil', 'G', 'ARR');  // filters
$fil['date_from'] = cot_import_date('fil_df', true, false, 'G');
$fil['date_to'] = cot_import_date('fil_dt', true, false, 'G');

$list_url_path = array('m' => 'other', 'p' => 'banners', 'n' => 'track');
if (empty($so))
{
	$so = 'track_date';
}
else
{
	$list_url_path['so'] = $so;
}
if (empty($w))
{
	$w = 'DESC';
}
else
{
	$list_url_path['w'] = $w;
}

$where = array();
$params = array();
$baWhere = array();

if (!empty($fil))
{
	foreach ($fil as $key => $val)
	{
		$val = trim(cot_import($val, 'D', 'TXT'));
		if (empty($val) && $val !== '0')
			continue;
		if (in_array($key, array('title')))
		{
			$params[$key] = "%{$val}%";
			$baWhere[] = "b.ba_title LIKE :$key";
			$list_url_path["fil[{$key}]"] = $val;
		}
		elseif ($key == 'date_from')
		{
			if ($fil[$key] == 0)
				continue;
			$where['filter'][] = "track_date >= '".$fil[$key]."'";
			$list_url_path["fil_df[year]"] = cot_date('Y', $fil[$key]);
			$list_url_path["fil_df[month]"] = cot_date('m', $fil[$key]);
			$list_url_path["fil_df[day]"] = cot_date('d', $fil[$key]);
		}
		elseif ($key == 'date_to')
		{
			if ($fil[$key] == 0)
				continue;
			$where['filter'][] = "track_date <= '".$fil[$key]."'";
			$list_url_path["fil_dt[year]"] = cot_date('Y', $fil[$key]);
			$list_url_path["fil_dt[month]"] = cot_date('m', $fil[$key]);
			$list_url_path["fil_dt[day]"] = cot_date('d', $fil[$key]);
		}
		else
		{
			$kkey = str_replace('.', '_', $key);
			$params[$kkey] = $val;
			if (mb_strpos($key, 'b.') === 0)
			{
				$baWhere[] = "$key = :$kkey";
			}
			else
			{
				$where['filter'][] = "$key = :$kkey";
			}
			$list_url_path["fil[{$key}]"] = $val;
		}
	}
	empty($where['filter']) || $where['filter'] = implode(' AND ', $where['filter']);
}
else
{
	$fil = array();
}

if (!empty($baWhere))
{
	$where['banners'] = "ba_id IN (SELECT b.ba_id FROM $db_ba_banners AS b WHERE ".implode(' AND ', $baWhere)." )";
}

$where = implode(' AND ', $where);

$res = $db->delete($db_ba_tracks, $where, $params);

if ($res > 0)
{
	cot_message(sprintf($L['ba_deleted_records'], $res));
}
else
{
	cot_message($L['ba_deleted_no']);
}

cot_redirect(cot_url('admin', $list_url_path, '', true));
