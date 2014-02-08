<?php

/* ====================
  [BEGIN_COT_EXT]
  Hooks=admin.extrafields.first
  [END_COT_EXT]
  ==================== */

/**
 * Page module
 *
 * @package page
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2014
 * @license BSD
 */
defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('banners', 'plug');
$extra_whitelist[$db_banners] = array(
	'name' => $db_banners,
	'caption' => $L['Plugin'].' Banners',
	'type' => 'plugin',
	'code' => 'banners',
	'tags' => array()
);
