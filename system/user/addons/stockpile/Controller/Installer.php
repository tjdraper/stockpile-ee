<?php

namespace BuzzingPixel\Stockpile\Controller;

use BuzzingPixel\Stockpile\Service\Install\ModuleInstaller;
use BuzzingPixel\Stockpile\Service\Install\ExtensionInstaller;

class Installer
{
	/**
	 * Install
	 */
	public function install()
	{
		// Add module
		ModuleInstaller::add();
	}

	/**
	 * Uninstall
	 */
	public function uninstall()
	{
		// Remove module
		ModuleInstaller::remove();
	}

	/**
	 * General update routines
	 */
	public function generalUpdate()
	{
		// Update module
		ModuleInstaller::update();
	}
}
