<?php

namespace BuzzingPixel\Stockpile\Service\Tag;

class Vars
{
	/**
	 * Get magic method
	 *
	 * @param string $name
	 * @return array
	 */
	public static function parse($vars, $namespace, $tagData)
	{
		// Make sure variables are zero indexed
		$vars = array_values($vars);

		// Get total
		$total = count($vars);

		// Add colon to namespace
		$namespace .= ':';

		// Start an array for variable keys
		$keys = array();

		// Start an array for the return values
		$returnVars = array();

		// Get keys to make sure each itteration has the key
		foreach ($vars as $key => $val) {
			foreach ($val as $var => $content) {
				$keys[$var] = $var;
			}
		}

		// Loop through the variables and namespace the vars,
		// add count variable, add missing keys if applicable
		foreach ($vars as $key => $val) {
			// Add missing keys as needed
			foreach ($keys as $keyCheck) {
				if (! isset($val[$keyCheck])) {
					$val[$keyCheck] = null;
				}
			}

			// Set count vars
			$val['index'] = $key;
			$val['count'] = $key + 1;
			$val['total_results'] = $total;

			// Namespace vars
			foreach ($val as $var => $content) {
				$returnVars[$key][$namespace . $var] = $content;
			}
		}

		return ee()->TMPL->parse_variables($tagData, $returnVars);
	}
}
