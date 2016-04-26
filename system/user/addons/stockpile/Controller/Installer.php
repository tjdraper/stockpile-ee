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

		// Add extension
		ExtensionInstaller::add();
	}

	/**
	 * Uninstall
	 */
	public function uninstall()
	{
		// Remove module
		ModuleInstaller::remove();

		// Remove extension
		ExtensionInstaller::remove();
	}

	/**
	 * General update routines
	 */
	public function generalUpdate()
	{
		// Update module
		ModuleInstaller::update();

		// Update extension
		ExtensionInstaller::update();
	}
}
