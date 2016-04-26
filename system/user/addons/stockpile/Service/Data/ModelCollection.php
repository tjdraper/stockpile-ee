<?php

namespace BuzzingPixel\Stockpile\Service\Data;

class ModelCollection implements \ArrayAccess, \Iterator, \Countable
{
	// Meta for itterator
	private $position = 0;
	protected $items = array();

	/**
	 * ModelCollection constructor
	 *
	 * @param array $array Array of models
	 */
	public function __construct($array)
	{
		// At some point in the future, this should probably be programmed
		// defensively to make sure all the items in the array are an instance
		// of a model
		$this->items = $array;
	}

	/**
	 * Set magic method
	 */
	public function __set($name, $value)
	{
		// Check if there are any items in this collection
		if (isset($this->items[0])) {
			// Get first item so we can ascertain information about the model
			$firstItem = $this->items[0];

			// Check if the property exists on the model
			if (
				property_exists($firstItem, $name) &&
				strpos($firstItem->{$name}, '_') !== 0
			) {
				// Loop through the models and set the property
				foreach ($this->items as $model) {
					$model->{$name} = $value;
				}
			}
		}

		// Prevent overloading
		return;
	}

	/**
	 * Get magic method
	 *
	 * @param string $name Class variable name
	 * @return mixed
	 */
	public function __get($name)
	{
		// Check if property exists and return it
		if (isset($this->{$name})) {
			return $this->{$name};
		}

		// Check if item exists and return it
		if (isset($this->items[$name])) {
			return $this->items[$name];
		}

		// Nothing exists to return
		return null;
	}

	/**
	 * Get all models as arrays
	 */
	public function asArray()
	{
		// Start an array for the items
		$array = array();

		// Loop through the models in the collection
		foreach ($this->items as $model) {
			// Run the model's asArray funciton
			$array[] = $model->asArray();
		}

		// Return the array
		return $array;
	}

	/**
	 * Get array of all model save values
	 *
	 * @param bool $preserveIds Optional for getting data for batch updates
	 * @return array
	 */
	public function getSaveValues($preserveIds = false)
	{
		// Start an array for the items
		$array = array();

		// Loop through the models in the collection
		foreach ($this->items as $model) {
			// Run the model's getSaveValues function
			$array[] = $model->getSaveValues($preserveIds);
		}

		// Return the array
		return $array;
	}

	/**
	 * Save all model data
	 *
	 * @return self
	 */
	public function save()
	{
		// Check if there are any items in this collection
		if (isset($this->items[0])) {
			// Get first item so we can ascertain information about the model
			$firstItem = $this->items[0];

			if (! $firstItem::$_tableName) {
				throw new \Exception(
					'This model does not have a table name and cannot be saved to the database'
				);
			}

			// Run the query
			ee()->db->update_batch(
				// Get the model table name
				$firstItem::$_tableName,
				// Get the array of values to update
				$this->getSaveValues(true),
				'id'
			);

			// Get the onAfterSave method for this model
			$onAfterSave = new \ReflectionMethod(
				get_class($firstItem),
				'onAfterSave'
			);

			// Make it accessible to us
			$onAfterSave->setAccessible(true);

			// Run after save method for each model in the collection
			foreach ($this->items as $model) {
				$onAfterSave->invoke($model);
			}
		}

		return $this;
	}

	/**
	 * Delete all model data
	 */
	public function delete()
	{
		// Check if there are any items in this collection
		if (isset($this->items[0])) {
			// Get first item so we can ascertain information about the model
			$firstItem = $this->items[0];

			if (! $firstItem::$_tableName) {
				throw new \Exception(
					'This model does not have a table name and cannot be deleted from the database'
				);
			}

			// Start an array for the IDs
			$ids = array();

			// Loop through each of the models
			foreach ($this->items as $key => $model) {
				// Save the ID
				$ids[] = $model->id;

				// Remove the model from the collection
				unset($this->items[$key]);
			}

			// Run the query to delete the model records
			ee()->db->where_in('id', $ids);
			ee()->db->delete($firstItem::$_tableName);
		}
	}

	/**
	 * Required ArrayAccess Methods
	 */
	public function offsetSet($offset, $value)
	{
		if (is_null($offset)) {
			$this->items[] = $value;
		} else {
			$this->items[$offset] = $value;
		}
	}

	public function offsetExists($offset)
	{
		return isset($this->items[$offset]);
	 }

	public function offsetUnset($offset)
	{
		unset($this->items[$offset]);
	}

	public function offsetGet($offset)
	{
		return isset($this->items[$offset]) ? $this->items[$offset] : null;
	}

	/**
	 * Required Iterator methods
	 */
	public function current()
	{
		return $this->items[$this->position];
	}

	function key()
	{
		return $this->position;
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
		return isset($this->items[$this->position]);
	}

	/**
	 * Countable required method
	 */
	public function count()
	{
		return count($this->items);
	}
}
