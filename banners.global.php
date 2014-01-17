<?php


/* ====================
  [BEGIN_COT_EXT]
  Hooks=global
  [END_COT_EXT]
  ==================== */

// Banners API is available everywhere
defined('COT_CODE') or die('Wrong URL.');
if (!defined('COT_ADMIN'))
{
	require_once cot_incfile('banners', 'plug');
}