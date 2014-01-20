<?php

/* ====================
  [BEGIN_COT_EXT]
  Hooks=tools
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
(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');


list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('plug', 'banners');
cot_block($usr['isadmin']);

require_once(cot_incfile('banners', 'plug'));
require_once cot_langfile('banners', 'plug');

//cot_rc_link_file($cfg['plugins_dir'].'/banners/tpl/admin.css');
// Роутер
// Only if the file exists...
if (!in_array($n, array('clients', 'track')))
{
	$n = 'main';
}
if(empty($a))
{
	$a = 'index';
}
if (file_exists(cot_incfile('banners', 'plug', 'admin.'.$n.'.'.$a)))
{
	$t = new XTemplate(cot_tplfile('banners.admin.'.$n.'.'.$a, 'plug'));
	require_once cot_incfile('banners', 'plug', 'admin.'.$n.'.'.$a);
	$t->parse('MAIN');
	$adminmain = $t->text('MAIN');
}
else
{
	// Error page
	cot_die_message(404);
	exit;
}