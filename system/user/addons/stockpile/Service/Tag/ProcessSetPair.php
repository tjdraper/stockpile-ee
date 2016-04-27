<?php

namespace BuzzingPixel\Stockpile\Service\Tag;

class ProcessSetPair
{
	// Properties
	private $pairData;

	/**
	 * Constructor
	 *
	 * @param object $tagParams
	 * @param string $tagData
	 */
	public function __construct(
		\BuzzingPixel\Stockpile\Service\Data\ParamsBase $tagParams,
		$tagData
	)
	{
		// Set the tag regex
		$LD = LD;
		$RD = RD;
		$ns = "{$tagParams->namespace}:";
		$regex = "/{$LD}{$ns}(.*?){$RD}(.*?){$LD}\/{$ns}(?:.*?){$RD}/s";

		// Get regex matches
		preg_match_all($regex, $tagData, $matches, PREG_SET_ORDER);

		$pairData = array();

		foreach ($matches as $key => $val) {
			$pairData[$val[1]] = trim($val[2]);
		}

		$this->pairData = $pairData;
	}

	/**
	 * Get magic method
	 *
	 * @param string $name
	 * @return array
	 */
	public function __get($name)
	{
		if ($name !== 'pairData') {
			return null;
		}

		return $this->pairData;
	}
}
