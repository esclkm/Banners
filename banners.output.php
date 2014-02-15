<?PHP

/* ====================
  [BEGIN_COT_EXT]
  Hooks=output
  [END_COT_EXT]
  ==================== */
/**
 * Cotonti Plugin Banners
 * Banner rotation plugin with statistics
 *
 * @package Banners
 * @author Alex
 * @copyright Portal30 2013 http://portal30.ru
 */
defined('COT_CODE') or die('Wrong URL.');

if (!defined('COT_ADMIN'))
{
	//require_once cot_incfile('banners', 'plug');
	$loaded_banners = banners_load();

	foreach ($loaded_banners as $key => $banner)
	{
		$output = str_replace("{BANNER_POSITION_".$key."}", $banner, $output);
	}
}

