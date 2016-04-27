<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

use BuzzingPixel\Stockpile\Controller\Tag;

class Stockpile
{
	/**
	 * Run Controller
	 *
	 * @param string $controllerMethod
	 * @param string $paramsClass
	 * @return string
	 */
	private function applyController($controllerMethod, $paramsClass = false)
	{
		$tagParams = ee()->TMPL->tagparams ?: array();
		$tagData = ee()->TMPL->tagdata ?: '';
		$tagController = new Tag($tagData, $tagParams, $paramsClass);
		return $tagController->{$controllerMethod}();
	}

	/**
	 * Set value
	 */
	public function set()
	{
		$this->applyController('set', 'SetParams');
	}

	/**
	 * Append value
	 */
	public function append()
	{
		$this->applyController('append', 'SetParams');
	}

	/**
	 * Prepend value
	 */
	public function prepend()
	{
		$this->applyController('prepend', 'SetParams');
	}

	/**
	 * Get value
	 *
	 * @return string
	 */
	public function get()
	{
		return $this->applyController('get', 'SetParams');
	}

	/**
	 * Set pair
	 */
	public function set_pair()
	{
		$this->applyController('setPair', 'SetParams');
	}

	/**
	 * Append pair
	 */
	public function append_pair()
	{
		$this->applyController('appendPair', 'SetParams');
	}

	/**
	 * Prepend pair
	 */
	public function prepend_pair()
	{
		$this->applyController('prependPair', 'SetParams');
	}

	/**
	 * Get pair
	 *
	 * @return string
	 */
	public function get_pair()
	{
		return $this->applyController('getPair', 'SetParams');
	}
}
