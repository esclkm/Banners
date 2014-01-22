<?php

defined('COT_CODE') or die('Wrong URL.');

global $db, $db_ba_tracks, $db_ba_banners, $sys, $cfg;

$id = cot_import('id', 'G', 'INT');
$banner = $db->query("SELECT b.*, c.* FROM $db_ba_banners AS b LEFT JOIN $db_ba_clients as c  ON b.bac_id=c.bac_id WHERE ba_id = ".(int)$id." LIMIT 1")->fetch();
if (!$banner)
{
	cot_diefatal('banner not found');
}

if ($cfg["plugin"]['banners']['track_clicks'] ||
	($banner['ba_track_clicks'] == 1) ||
	($banner['ba_track_clicks'] == -1 && $banner['bac_track_clicks'] == 1))
{
	banner_impress($id, 'click');
}

if (!empty($banner['ba_clickurl']))
{
	header('Location: '.$banner['ba_clickurl']);
}
exit();
