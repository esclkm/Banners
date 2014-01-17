<?php

/* ====================
  [BEGIN_COT_EXT]
  Hooks=admin.structure.first
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
defined('COT_CODE') or die('Wrong URL');

$extension_structure[] = 'banners';
if ($n = 'banners')
{
	require_once(cot_incfile('banners', 'plug'));
	require_once cot_langfile('banners', 'plug');

	$t = new XTemplate(cot_tplfile('banners.admin.structure', 'plug'));
}
