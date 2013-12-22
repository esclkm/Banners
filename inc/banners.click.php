<?php

defined('COT_CODE') or die('Wrong URL.');


global $cfg;

$id = cot_import('id', 'G', 'INT');
$banner = $db->query("SELECT b.*, c.* FROM $db_ba_banners AS b LEFT JOIN $db_ba_clients as c  ON b.bac_id=c.bac_id WHERE ba_id = ".(int)$id." LIMIT 1")->fetch();
if (!$banner)
{
	cot_diefatal('banner not found');
}


global $db, $db_ba_tracks, $sys;
$db->query("UPDATE $db_ba_banners SET ba_clicks = ba_clicks+1 WHERE ba_id = ".(int)$id." LIMIT 1");

global $cfg;


if ($cfg["plugin"]['banners']['track_clicks'] ||
	($banner['ba_track_clicks'] == 1) ||
	($banner['ba_track_clicks'] == -1 && $banner['bac_track_clicks'] == 1))
{
	$trackDate = date('Y-m-d H', $sys['now']).':00:00';

	$sql = "SELECT `track_count` FROM $db_ba_tracks
                WHERE track_type=2 AND ba_id={$banner['ba_id']} AND track_date='{$trackDate}'";

	$count = $db->query($sql)->fetchColumn();

	if ($count)
	{
		// update count
		$data = array(
			'track_count' => $count + 1
		);
		$db->update($db_ba_tracks, $data, "track_type=2 AND ba_id={$banner['ba_id']} AND track_date='{$trackDate}'");
	}
	else
	{
		// insert new count
		$data = array(
			'track_count' => 1,
			'track_type' => 2,
			'ba_id' => (int)$banner['ba_id'],
			'track_date' => $trackDate
		);
		$db->insert($db_ba_tracks, $data);
	}
}

if (!empty($banner['ba_clickurl']))
{
	header('Location: '.$banner['ba_clickurl']);
}
exit();
