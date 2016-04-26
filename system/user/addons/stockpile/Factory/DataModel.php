<?php

namespace BuzzingPixel\Stockpile\Factory;

use BuzzingPixel\Stockpile\Service\Data\ModelCollection;

class DataModel
{
	// Model name
	private $modelName;

	// Model class
	private $modelClass;

	/**
	 * Model constructor
	 *
	 * $param string $name The name of the model to get
	 */
	public function __construct($name = null)
	{
		$addonInfo = ee('Addon')->get('stockpile');

		// Set the model name
		$this->modelName = $name;

		// Get the model class
		$this->modelClass = '\\' . $addonInfo->get('namespace') . '\\Model\\' . $this->modelName;
	}

	/**
	 * Populate a model with data
	 *
	 * @param array $data Array of data to populate models with
	 * @return object Model
	 */
	public function populateModel($data)
	{
		$model = new $this->modelClass();

		// Populate it with the data
		foreach ($data as $key => $val) {
			$model->{$key} = $val;
		}

		return $model;
	}

	/**
	 * Populate a model collection with data
	 *
	 * @param array $data Array of data to populate models with
	 * @return object Collection
	 */
	public function collection($data)
	{
		// Loop through the data and populate the model
		foreach ($data as $key => $item) {
			// Create a new model class
			$model = new $this->modelClass();

			// Populate it with the data
			foreach ($item as $k => $v) {
				$model->{$k} = $v;
			}

			// Replace the old array value with the model value
			$data[$key] = $model;
		}

		// Return the collection of models
		return new ModelCollection($data);
	}
}
