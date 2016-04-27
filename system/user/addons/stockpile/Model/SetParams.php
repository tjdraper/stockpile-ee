<?php

namespace BuzzingPixel\Stockpile\Model;

use BuzzingPixel\Stockpile\Service\Data\ParamsBase;


class SetParams extends ParamsBase
{
	protected $name = 'string';
	protected $value = 'string';
	protected $namespace = 'string';
	protected $limit = 'int';
	protected $offset = 'int';

	/**
	 * Set default namespace
	 */
	protected function _namespace_default()
	{
		return 'stockpile';
	}
}
