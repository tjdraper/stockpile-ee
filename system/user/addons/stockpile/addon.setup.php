<?php

$addonJson = json_decode(
	file_get_contents(PATH_THIRD . 'stockpile/addon.json')
);

defined('STOCKPILE_NAME') || define('STOCKPILE_NAME', $addonJson->label);
defined('STOCKPILE_VER') || define('STOCKPILE_VER', $addonJson->version);

return array(
	'author' => $addonJson->author,
	'author_url' => $addonJson->authorUrl,
	'description' => $addonJson->description,
	'docs_url' => $addonJson->docsUrl,
	'name' => $addonJson->label,
	'namespace' => $addonJson->namespace,
	'settings_exist' => $addonJson->settingsExist,
	'version' => $addonJson->version,
);
