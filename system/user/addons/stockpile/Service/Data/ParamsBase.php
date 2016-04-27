<?php

namespace BuzzingPixel\Stockpile\Service\Data;

abstract class ParamsBase
{
	/**
	 * Baseparams constructor
	 *
	 * @param array $params
	 */
	public function __construct($params = array())
	{
		$class = new \ReflectionClass($this);
		$classProperties = $class->getProperties();
		$staticClassProperties = $class->getProperties(\ReflectionProperty::IS_STATIC);
		$classMethods = $class->getMethods();
		$classProperties = array_diff($classProperties, $staticClassProperties);

		// Loop through the params and set them
		foreach ($classProperties as $property) {
			$key = $property->name;
			$val = $this->{$property->name};
			$param = isset($params[$key]) ? $params[$key] : null;
			$param = preg_replace('/^{.*?}/', '', $param);

			// If the param is not set, check if there is a fallback
			if (! $param) {
				$match = false;

				// Check for a method to set the default
				foreach ($classMethods as $method) {
					if ($method->name === "_{$key}_default") {
						$param = $this->{$method->name}();

						$match = true;

						break;
					}
				}

				// If no match, check for a property default
				if (! $match) {
					foreach ($classProperties as $prop) {
						if ($prop->name === "_{$key}_default") {
							$param = $this->{$prop->name};

							break;
						}
					}
				}
			}

			// Process custom params
			if (strpos($val, 'custom|') === 0) {
				$ex = explode('|', $val);
				$method = $ex[1];

				$this->{$key} = $this->{$method}($param);

			// Set string value
			} elseif ($val === 'string') {
				$this->{$key} = $param;

			// Set int value
			} elseif ($val === 'int') {
				$this->{$key} = $param !== null ? (int) $param : null;

			// Set float value
			} elseif ($val === 'float') {
				$this->{$key} = $param !== null ? (float) $param : null;

			// Set array value
			} elseif (
				$val === 'array' ||
				$val === 'intArray' ||
				$val === 'floatArray'
			) {
				$param = $param ? explode('|', $param) : array();

				// Set array as ints
				if ($val === 'intArray') {
					foreach ($param as $pKey => $pVal) {
						$param[$pKey] = (int) $pVal;
					}

				// Set array as floats
				} elseif ($val === 'floatArray') {
					foreach ($param as $pKey => $pVal) {
						$param[$pKey] = (float) $pVal;
					}
				}

				$this->{$key} = $param;

			// Set truthy value
			} elseif ($val === 'truthy') {
				$this->{$key} = $this->truthy($param);

			// Set falsy value
			} elseif ($val === 'falsy') {
				$this->{$key} = $this->falsy($param);

			// Otherwise this type isn't account for and should be unset
			} else {
				unset($this->{$key});
			}
		}
	}

	/**
	 * Get truthy value
	 *
	 * @param string|bool $val
	 */
	private function truthy($val)
	{
		$truth = array(
			'y',
			'yes',
			'true',
			true
		);

		return in_array($val, $truth, true);
	}

	/**
	 * Get falsy value
	 *
	 * @Param string|bool $val
	 */
	private function falsy($val)
	{
		$false = array(
			'n',
			'no',
			'false',
			false
		);

		return ! in_array($val, $false, true);
	}

	/**
	 * Get param magic method
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		if (isset($this->{$name})) {
			return $this->{$name};
		}

		return null;
	}

	/**
	 * Set param magic method
	 *
	 * @param string $name
	 * @param mixed $val
	 * @return null
	 */
	public function __set($name, $val)
	{
		return null;
	}

	/**
	 * Get an md5 hash of all params
	 *
	 * @return string
	 */
	public function getHash()
	{
		return md5(serialize($this));
	}
}
