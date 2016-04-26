<?php

namespace BuzzingPixel\Stockpile\Service\Data;

use BuzzingPixel\Stockpile\Library\DefaultDatabaseTypes;
use BuzzingPixel\Stockpile\Utility\ReflectionUtility;

abstract class ModelBase implements \Iterator
{
	// Primary key
	private $id;
	protected static $_primary_key = 'id';

	// Meta for itterator
	private $position = 0;
	private $itteratorKeys = array();

	// Source table name
	public static $_tableName = null;

	// Property types
	private $_propertiesTypes = array();

	// Preset properties
	private $_presetProperties = array();

	// Property database column info
	private $_dbColumnInfo = array();

	// Is model deleted?
	private $deleted = false;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// If this is an installable model, we need to set the primary key
		if (static::$_tableName) {
			// Set the primary key
			$this->itteratorKeys[] = static::$_primary_key;

			// Set the primary key type
			$this->_propertiesTypes[static::$_primary_key] = 'int';

			// Set the primary key database type
			$this->_dbColumnInfo[static::$_primary_key] =
				DefaultDatabaseTypes::$map['int'];
		}

		// Get properties
		$properties = ReflectionUtility::getProtectedNonStaticProps($this);

		// Get methods
		$class = new \ReflectionClass($this);
		$protecteMethods = $class->getMethods(\ReflectionMethod::IS_PROTECTED);

		// Set preset properties
		foreach ($properties as $prop) {
			$prop->setAccessible(true);

			if ($prop->getValue($this) === 'preset') {
				$this->_presetProperties[$prop->name] = $prop->name;
			}
		}

		// Loop through the properties
		foreach ($properties as $prop) {
			// Add the key to the itterator keys array
			$this->itteratorKeys[] = $prop->name;

			// Make the property accessible to get and set value via reflection
			$prop->setAccessible(true);

			// Get the property value
			$value = $prop->getValue($this);
			$valueType = gettype($value);

			// Set the property type
			if ($valueType === 'string') {
				$this->_propertiesTypes[$prop->name] = $value;
			} else {
				$this->_propertiesTypes[$prop->name] = $value['type'];
			}

			// Set db column types if applicable
			if (static::$_tableName) {
				if ($valueType === 'string') {
					$this->_dbColumnInfo[$prop->name] =
						DefaultDatabaseTypes::$map[$value];
				} else {
					$this->_dbColumnInfo[$prop->name] = $value['db'];
				}
			}

			// Set initial value to null
			$prop->setValue($this, null);

			// Check for onSetup method
			foreach ($protecteMethods as $method) {
				if ("{$prop->name}__onSetup" === $method->name) {
					$prop->setValue($this, $this->{$method->name}());

					break;
				}
			}
		}
	}

	/**
	 * Get magic method
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		// Make sure property exists and does not have a private indicator
		if (
			property_exists($this, $name) &&
			strpos($name, '_') !== 0
		) {
			$value = $this->{$name};

			// Check for onGet method
			$modelMethods = get_class_methods($this);

			// Loop through the model methods
			foreach ($modelMethods as $methodName) {
				// Explode the name to look for onGet
				$check = explode('__', $methodName);

				// Check if we have a method to run for onSet
				if (
					count($check) === 2 &&
					$check[0] === $name &&
					$check[1] === 'onGet'
				) {
					$value = $this->{$methodName}($value);

					break;
				}
			}

			return $value;
		}
	}

	/**
	 * Set magic method
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value)
	{
		/**
		 * Initial check to see if property can be set
		 *
		 * make sure the name is not the primary key,
		 * the primary key has not already been set
		 * and the model hasn't been deleted
		 *
		 * We don't want the primary key to be changed once it's been set
		 * It should generally be set by the model factory or the model itself
		 * after the initial query has run
		 */
		if (
			$name === 'id' &&
			$this->id !== null &&
			! property_exists($this, 'deleted')
		) {
			return;
		}

		// Make sure property is settable
		if (isset($this->_propertiesTypes[$name])) {
			$type = $this->_propertiesTypes[$name];

			// Cast value properly
			if ($type === 'int') {
				$value = (int) $value;
			} elseif ($type === 'float') {
				$value = (float) $value;
			} elseif ($type === 'array') {
				if (gettype($value) !== 'array') {
					$value = explode('|', $value);
				}
			} elseif ($type === 'bool') {
				if (gettype($value) !== 'boolean') {
					$value = $value === 'y';
				}
			} elseif ($type === 'string') {
				$value = (string) $value;
			}

			// Check for onSet method
			$modelMethods = get_class_methods($this);

			// Loop through the model methods
			foreach ($modelMethods as $methodName) {
				// Explode the name to look for onSet
				$check = explode('__', $methodName);

				// Check if we have a method to run for onSet
				if (
					count($check) === 2 &&
					$check[0] === $name &&
					$check[1] === 'onSet'
				) {
					// Get the value from the method
					$value = $this->{$methodName}($value);

					// Found a method, no need to go on
					break;
				}
			}

			// Set the value
			$this->{$name} = $value;
		}

		// Do not allow overloading
		return;
	}

	/**
	 * Get property type
	 *
	 * @param string $property
	 * @return string
	 */
	public function getType($property)
	{
		// Make sure the property type requested is set
		if (! isset($this->_propertiesTypes[$property])) {
			return null;
		}

		// Return the property type
		return $this->_propertiesTypes[$property];
	}

	/**
	 * Get database columns
	 *
	 * @return array
	 */
	public function getDbColumnInfo()
	{
		return $this->_dbColumnInfo;
	}

	/**
	 * Get primary key
	 *
	 * @return array
	 */
	public function getPrimaryKey()
	{
		return static::$_primary_key;
	}

	/**
	 * Save model to database
	 *
	 * @return self
	 */
	public function save()
	{
		if (! static::$_tableName) {
			throw new \Exception(
				'This model does not have a table name and cannot be saved to the database'
			);
		}

		// Create new record if there is no id
		if (! $this->id) {
			ee()->db->insert(static::$_tableName, $this->getSaveValues());
			$this->id = ee()->db->insert_id();

		// Update the record if there is an id
		} else {
			// Update record
			ee()->db->where('id', $this->id);
			ee()->db->update(static::$_tableName, $this->getSaveValues());
		}

		// No longer deleted in case this model class was previously deleted
		$this->delete = false;

		// Run onAfterSave method
		$this->onAfterSave();

		// Return the model
		return $this;
	}

	/**
	 * Runs after save
	 *
	 * placeholder function can be used when extending
	 */
	protected function onAfterSave()
	{
		return;
	}

	/**
	 * Delete the row from the database
	 */
	public function delete()
	{
		if (! static::$_tableName) {
			throw new \Exception(
				'This model does not have a table name and cannot be deleted from the database'
			);
		}

		// Delete the record from the database
		ee()->db->where('id', $this->id);
		ee()->db->delete(static::$_tableName);

		// This model has been deleted
		$this->deleted = true;

		// Set all properties to null
		foreach ($this as $key => $val) {
			$this->{$key} = null;
		}
	}

	/**
	 * Get the model data as an array
	 */
	public function asArray()
	{
		// Start an array
		$array = array();

		// Loop through the properties
		foreach ($this as $key => $val) {
			// Add the property to the array
			$array[$key] = $val;
		}

		// Return the array
		return $array;
	}

	/**
	 * Get array of save values
	 *
	 * @param bool $preserveId Optional for getting data for batch updates
	 * @return array
	 */
	public function getSaveValues($preserveId = false)
	{
		// Set data to be updated
		$saveValues = $this->asArray();

		// Remove the ID from the update data. We never want to set that
		// (except for batch updating)
		// It either gets set on the DB on new entry, or it exists and should
		// not be updated
		if (! $preserveId) {
			unset($saveValues['id']);
		}

		// Get model methods
		$modelMethods = get_class_methods($this);

		// Check data type and convert to save value as needed
		foreach ($saveValues as $key => $val) {
			// Check the type for bool
			if ($this->getType($key) === 'bool') {
				$saveValues[$key] = $val === true ? 'y' : 'n';

			// Check for type of array
			} elseif ($this->getType($key) === 'array') {
				$saveValues[$key] = implode('|', $val);
			}

			// Loop through the model methods
			foreach ($modelMethods as $methodName) {
				// Explode the name to look for onSave
				$check = explode('__', $methodName);

				// Check if we have a method to run for onSave
				if (
					count($check) === 2 &&
					$check[0] === $key &&
					$check[1] === 'onSave'
				) {
					// Get the value from the method
					$saveValues[$key] = $this->{$methodName}();

					// We found our method, we should break the foreach loop
					break;
				}
			}
		}

		// Return the save values
		return $saveValues;
	}

	/**
	 * Get array item
	 *
	 * @param string $property
	 * @param string $arrayItem
	 * @return mixed
	 */
	public function getArrayItem($property, $arrayItem)
	{
		if (isset($this->{$property}[$arrayItem])) {
			return $this->{$property}[$arrayItem];
		}

		return null;
	}

	/**
	 * Check if array value exists and return the key
	 *
	 * @param string $property
	 * @param string $arrayValue
	 * @return mixed
	 */
	public function checkArrayValue($property, $arrayValue)
	{
		if (gettype($this->{$property}) !== 'array') {
			return false;
		}

		return array_search($arrayValue, $this->{$property});
	}

	/**
	 * Required Iterator methods
	 */
	public function current()
	{
		return $this->{$this->itteratorKeys[$this->position]};
	}

	function key()
	{
		return $this->itteratorKeys[$this->position];
	}

	function next()
	{
		++$this->position;
	}

	function rewind()
	{
		$this->position = 0;
	}

	function valid()
	{
		return isset($this->itteratorKeys[$this->position]);
	}
}
