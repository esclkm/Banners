<?php

defined('COT_CODE') or die('Wrong URL.');

global $db, $db_banner_tracks, $db_banners, $sys, $cfg;

$id = cot_import('id', 'G', 'INT');
$banner = $db->query("SELECT b.*, c.* FROM $db_banners AS b LEFT JOIN $db_banner_clients as c  ON b.bac_id=c.bac_id WHERE ba_id = ".(int)$id." LIMIT 1")->fetch();
if (empty($banner))
{
	cot_die_message(404, TRUE);
}
if(!$banner['ba_published'])
{
	cot_die_message(602, TRUE);
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
