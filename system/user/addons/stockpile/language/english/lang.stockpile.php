<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

$addonInfo = ee('Addon')->get('stockpile');

$lang = array(
	// Module required lang
	'stockpile_module_name' => $addonInfo->get('name'),
	'stockpile_module_description' => $addonInfo->get('description'),
);
