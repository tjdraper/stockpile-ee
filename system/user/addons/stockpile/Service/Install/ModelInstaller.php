<?php

namespace BuzzingPixel\Stockpile\Service\Install;

use BuzzingPixel\Stockpile\Utility\Table;

class ModelInstaller
{
	/**
	 * Install a model
	 *
	 * @param string $name The name of the model class
	 */
	public static function install($name)
	{
		$addonInfo = ee('Addon')->get('stockpile');

		// Get the model class
		$modelClass = '\\' . $addonInfo->get('namespace') . '\\Model\\' . $name;
		$modelClass = new $modelClass;

		// Check to make sure this model is installable
		if (! $modelClass::$_tableName) {
			throw new \Exception(
				'This model does not have a table name and cannot be installed'
			);
		}

		// If the table is already installed, halt method
		if (ee()->db->table_exists($modelClass::$_tableName)) {
			return;
		}

		// Insert the table
		Table::insert(
			$modelClass->getDbColumnInfo(),
			$modelClass->getPrimaryKey(),
			$modelClass::$_tableName
		);
	}

	/**
	 * Uninstall a model
	 *
	 * @param string $name The name of the model class
	 */
	public static function uninstall($name)
	{
		$addonInfo = ee('Addon')->get('stockpile');

		// Get the model class
		$modelClass = '\\' . $addonInfo->get('namespace') . '\\Model\\' . $name;
		$modelClass = new $modelClass;

		// Check to make sure this model is installable
		if (! $modelClass::$_tableName) {
			throw new \Exception(
				'This model does not have a table name and cannot be removed'
			);
		}

		// If the table is not installed, halt method
		if (! ee()->db->table_exists($modelClass::$_tableName)) {
			return;
		}

		// Remove the table
		Table::remove($modelClass::$_tableName);
	}
}
