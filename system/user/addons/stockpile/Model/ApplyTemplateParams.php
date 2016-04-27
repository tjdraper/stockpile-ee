<?php

namespace BuzzingPixel\Stockpile\Model;

use BuzzingPixel\Stockpile\Service\Data\ParamsBase;


class ApplyTemplateParams extends ParamsBase
{
	protected $template = 'string';
	protected $namespace = 'string';
	protected $pair = 'string';

	/**
	 * Set default namespace
	 */
	protected function _namespace_default()
	{
		return 'stockpile';
	}
}
