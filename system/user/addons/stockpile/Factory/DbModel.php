<?php

namespace BuzzingPixel\Stockpile\Factory;

use BuzzingPixel\Stockpile\Service\Data\ModelCollection;

class DbModel
{
	// Model name
	private $modelName;

	// Model class
	private $modelClass;

	// Table name
	private $tableName;

	// Filters array
	private $filters = array();

	// Ordering array
	private $ordering = array();

	// Limit
	private $limit;

	// Filter query map
	private $filterMap = array(
		'==' => 'where',
		'!=' => 'where',
		'<' => 'where',
		'>' => 'where',
		'<=' => 'where',
		'>=' => 'where',
		'IN' => 'where_in',
		'NOT IN' => 'where_not_in'
	);

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

		// Set the model table name
		$tableNameProperty = new \ReflectionProperty(
			$this->modelClass, '_tableName'
		);
		$this->tableName = $tableNameProperty->getValue();

		if (! $this->tableName) {
			throw new \Exception(
				'This model does not have a table name and cannot be retrieved'
			);
		}
	}

	/**
	 * Get a model
	 *
	 * @param string $name The name of the model to get
	 * @return self
	 */
	public function get($name)
	{
		$this->modelName = $name;
		return $this;
	}

	/**
	 * Filter the model
	 *
	 * @param string $filterOn
	 * @param mixed $condition
	 * @param mixed $value
	 * @return self
	 */
	public function filter($filterOn, $condition, $value = null)
	{
		// Check if $condition is a condition or a value
		if ($value === null) {
			$value = $condition;
			$condition = '==';
		}

		// Throw an error if the condition is not an accepted condition
		if (! isset($this->filterMap[$condition])) {
			throw new \Exception('Conditional parameter not allowed');
		}

		// Add the filter to the filters array
		$this->filters[] = compact(
			'filterOn', 'condition', 'value'
		);

		// Return the instance of the model factory
		return $this;
	}

	/**
	 * Order the model
	 *
	 * @param string $by
	 * @param string $sort
	 * @return self
	 */
	public function order($by, $sort = 'DESC')
	{
		// Set the order to the ordering array
		$this->ordering[] = compact(
			'by',
			'sort'
		);

		// Return the instance of the model factory
		return $this;
	}

	/**
	 * Set model limit
	 *
	 * @param int $limit
	 * @return self
	 */
	public function limit($limit)
	{
		// Set the limit
		$this->limit = $limit;

		// Return the instance of the model factory
		return $this;
	}

	/**
	 * Get first result
	 *
	 * @return object An instance of the requested model
	 */
	public function first()
	{
		// Save original limit
		$originalLimit = $this->limit;

		// If we're getting the first result, we know the limit should be 1
		$this->limit = 1;

		// Run the query
		$models = $this->runQuery();

		// Reset the limit
		$this->limit = $originalLimit;

		// If there are no models, return an empty model class
		if (! $models) {
			return new $this->modelClass();
		}

		// Return the first model
		return $models[0];
	}

	/**
	 * Get all results
	 *
	 * @return object Model collection
	 */
	public function all()
	{
		// Run the query
		$models = $this->runQuery();

		// Return the collection of models
		return new ModelCollection($models);
	}

	/**
	 * Delete models matching criteriea
	 */
	public function delete()
	{
		// Apply the filters
		$this->applyFilters();

		// Delete applicable items from the database
		ee()->db->delete($this->tableName);
	}

	/**
	 * Run the query
	 */
	private function runQuery()
	{
		// Start the query
		ee()->db->select('*')
			->from($this->tableName);

		// Apply the filters
		$this->applyFilters();

		// Get the result
		$result = ee()->db->get()->result();

		// Start an array for the models
		$models = array();

		// Get a model of each result
		foreach ($result as $data) {
			// Create a new model class
			$model = new $this->modelClass();

			// Populate it with the data from the result
			foreach ($data as $key => $item) {
				$model->{$key} = $item;
			}

			// Add the model to the array
			$models[] = $model;
		}

		// Return the models array
		return $models;
	}

	/**
	 * Apply filters
	 */
	private function applyFilters()
	{
		// Apply filters
		foreach ($this->filters as $filter) {
			if ($this->filterMap[$filter['condition']] === 'where') {
				if ($filter['condition'] === '==') {
					ee()->db->where($filter['filterOn'], $filter['value']);
				} else {
					ee()->db->where(
						$filter['filterOn'] . ' ' . $filter['condition'],
						$filter['value']
					);
				}
			} else {
				ee()->db->{$this->filterMap[$filter['condition']]}(
					$filter['filterOn'],
					$filter['value']
				);
			}
		}

		// Apply ordering
		foreach ($this->ordering as $ordering) {
			ee()->db->order_by($ordering['by'], $ordering['sort']);
		}

		// Apply limit
		if ($this->limit) {
			ee()->db->limit($this->limit);
		}
	}
}
