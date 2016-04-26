<?php

namespace BuzzingPixel\Stockpile\Utility;

class ReflectionUtility
{
	/**
	 * Get protected non-static properties
	 *
	 * @param object $class
	 */
	public static function getProtectedNonStaticProps($class)
	{
		// Get the reflection class
		$reflection = new \ReflectionClass($class);

		// Get protected properties
		$properties = $reflection->getProperties(
			\ReflectionProperty::IS_PROTECTED
		);

		// Loop through the properties
		foreach ($properties as $key => $prop) {
			// Check if item is static and remove it
			if ($prop->isStatic()) {
				unset($properties[$key]);
			}
		}

		return array_values($properties);
	}
}
