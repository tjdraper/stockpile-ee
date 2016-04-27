<?php

namespace BuzzingPixel\Stockpile\Service\Tag;

class PrefixedParams
{
	// Properties
	private $params = array();

	/**
	 * Constructor
	 *
	 * @param string $prefix
	 * @param array $params
	 */
	public function __construct($prefix, $params)
	{
		// Get prefix length
		$prefixLength = strlen($prefix) + 1;

		// Loop through the params
		foreach ($params as $key => $val) {
			// Check if any params start with the prefix
			if (strncmp($key, "{$prefix}:", $prefixLength) === 0) {
				// Add the param to the array
				$this->params[substr($key, $prefixLength)] = $val;
			}
		}
	}

	/**
	 * Get magic method
	 *
	 * @param string $name
	 * @return array
	 */
	public function __get($name)
	{
		if ($name !== 'params') {
			return null;
		}

		return $this->params;
	}
}
