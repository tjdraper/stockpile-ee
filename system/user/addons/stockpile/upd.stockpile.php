<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

use BuzzingPixel\Stockpile\Controller\Installer;

class Stockpile_upd
{
	public $name = STOCKPILE_NAME;
	public $version = STOCKPILE_VER;

	private $installer;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->installer = new Installer();
	}

	/**
	 * Install
	 *
	 * @return bool
	 */
	public function install()
	{
		$this->installer->install();
		return true;
	}

	/**
	 * Uninstall
	 *
	 * @return bool
	 */
	public function uninstall()
	{
		$this->installer->uninstall();
		return true;
	}

	/**
	 * Update
	 *
	 * @param string $current The current version before update
	 * @return bool
	 */
	public function update($current = '')
	{
		$addonInfo = ee('Addon')->get('stockpile');

		if ($current === $addonInfo->get('version')) {
			return false;
		}

		$this->installer->generalUpdate();
		return true;
	}
}
