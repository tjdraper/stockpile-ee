<?php

namespace BuzzingPixel\Stockpile\Controller;

use BuzzingPixel\Stockpile\Service\Tag\ProcessSetPair;
use BuzzingPixel\Stockpile\Service\Tag\Vars;

class Tag
{
	// Class properties
	private $tagData;
	private $tagParams;

	/**
	 * Tag Constructor
	 *
	 * @param array $tagParams
	 * @param string $tagData
	 */
	public function __construct($tagData = '', $tagParams = array(), $tagModel = '')
	{
		$this->tagData = $tagData;

		if ($tagModel) {
			$paramsClass = "BuzzingPixel\\Stockpile\\Model\\{$tagModel}";
		} else {
			$paramsClass = "BuzzingPixel\\Stockpile\\Model\\BlankParams";
		}

		$this->tagParams = new $paramsClass($tagParams);
	}

	/**
	 * Set tag
	 */
	public function set()
	{
		// Check if we have a name to set
		if (! $this->tagParams->name) {
			return;
		}

		// Get the value
		$val = trim($this->tagParams->value ?: $this->tagData);

		// Set the value to cache
		ee()->session->set_cache('stockpileSet', $this->tagParams->name, $val);
	}

	/**
	 * Append tag
	 */
	public function append()
	{
		// Check if we have a name to set
		if (! $this->tagParams->name) {
			return;
		}

		// Get previous value of this set
		$content = ee()->session->cache('stockpileSet', $this->tagParams->name);
		$content = $content ?: '';

		// Get the value
		$val = trim($this->tagParams->value ?: $this->tagData);

		// Set the value to cache
		ee()->session->set_cache(
			'stockpileSet',
			$this->tagParams->name,
			$content . $val
		);
	}

	/**
	 * Prepend tag
	 */
	public function prepend()
	{
		// Check if we have a name to set
		if (! $this->tagParams->name) {
			return;
		}

		// Get the previous value of this set
		$content = ee()->session->cache('stockpileSet', $this->tagParams->name);
		$content = $content ?: '';

		// Get the value
		$val = trim($this->tagParams->value ?: $this->tagData);

		// Set the value to cache
		ee()->session->set_cache(
			'stockpileSet',
			$this->tagParams->name,
			$val . $content
		);
	}

	/**
	 * Get tag
	 *
	 * @return string
	 */
	public function get()
	{
		return ee()->session->cache('stockpileSet', $this->tagParams->name);
	}

	/**
	 * Set Pair tag
	 */
	public function setPair()
	{
		// Check if we have a name to set
		if (! $this->tagParams->name) {
			return;
		}

		// Get the tag pair set data
		$processSetPair = new ProcessSetPair($this->tagParams, $this->tagData);

		// Check if we have data to set
		if (! $processSetPair->pairData) {
			return;
		}

		// Set the value to cache
		ee()->session->set_cache(
			'stockpilePair',
			$this->tagParams->name,
			array(
				$processSetPair->pairData
			)
		);
	}

	/**
	 * Append Pair tag
	 */
	public function appendPair()
	{
		// Check if we have a name to set
		if (! $this->tagParams->name) {
			return;
		}

		// Get the tag pair set data
		$processSetPair = new ProcessSetPair($this->tagParams, $this->tagData);

		// Check if we have data to set
		if (! $processSetPair->pairData) {
			return;
		}

		// Get the value
		$val = ee()->session->cache('stockpilePair', $this->tagParams->name);
		$val = $val ?: array();

		// Append the data
		$val[] = $processSetPair->pairData;

		// Set the value to cache
		ee()->session->set_cache('stockpilePair', $this->tagParams->name, $val);
	}

	/**
	 * Prepend Pair tag
	 */
	public function prependPair()
	{
		// Check if we have a name to set
		if (! $this->tagParams->name) {
			return;
		}

		// Get the tag pair set data
		$processSetPair = new ProcessSetPair($this->tagParams, $this->tagData);

		// Check if we have data to set
		if (! $processSetPair->pairData) {
			return;
		}

		// Get the value
		$val = ee()->session->cache('stockpilePair', $this->tagParams->name);
		$val = $val ?: array();

		// Prepend the data
		array_unshift($val, $processSetPair->pairData);

		// Set the value to cache
		ee()->session->set_cache('stockpilePair', $this->tagParams->name, $val);
	}

	/**
	 * Get Pair tag
	 *
	 * @return string
	 */
	public function getPair()
	{
		// Get the pair
		$vars = ee()->session->cache('stockpilePair', $this->tagParams->name);
		$vars = $vars ?: array();

		// Set limit default
		$limit = $this->tagParams->limit ?: 999999999999999;

		// Get Limit and Offset
		$vars = array_slice($vars, $this->tagParams->offset, $limit);

		// If no vars, return empty string
		if (! $vars) {
			return '';
		}

		// Parse variables
		return Vars::parse($vars, $this->tagParams->namespace, $this->tagData);
	}
}
