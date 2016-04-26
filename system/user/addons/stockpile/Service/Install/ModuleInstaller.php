<?php

namespace BuzzingPixel\Stockpile\Service\Install;

class ModuleInstaller
{
	/**
	 * Add module record
	 */
	public static function add()
	{
		$addonInfo = ee('Addon')->get('stockpile');

		ee()->db->insert('modules', array(
			'module_name' => 'Stockpile',
			'module_version' => $addonInfo->get('version'),
			'has_cp_backend' => 'n',
			'has_publish_fields' => 'n'
		));
	}

	/**
	 * Remove module record
	 */
	public static function remove()
	{
		ee()->db->where('module_name', 'Stockpile');
		ee()->db->delete('modules');
	}

	/**
	 * Update module record
	 */
	public static function update()
	{
		$addonInfo = ee('Addon')->get('stockpile');

		ee()->db->where('module_name', 'Stockpile');
		ee()->db->update('modules', array(
			'module_version' => $addonInfo->get('version')
		));
	}
}
