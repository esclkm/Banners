<?PHP

/* ====================
  [BEGIN_COT_EXT]
  Hooks=standalone
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

require_once cot_incfile('banners', 'plug');
require_once cot_langfile('banners');

if ($a != 'ajax')
{
	$a = 'click';
}
require_once cot_incfile('banners', 'plug', $a);
