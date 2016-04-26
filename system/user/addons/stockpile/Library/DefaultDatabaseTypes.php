<?php

namespace BuzzingPixel\Stockpile\Library;

class DefaultDatabaseTypes
{
	/**
	 * Countries
	 *
	 * @var string
	 */
	public static $map = array(
		'int' => array(
			'type' => 'INT',
			'unsigned' => true
		),
		'float' => array(
			'type' => 'DECIMAL',
			'constraint' => '30,10'
		),
		'array' => array(
			'type' => 'TEXT'
		),
		'string' => array(
			'type' => 'TEXT'
		),
		'bool' => array(
			'type' => 'CHAR',
			'length' => 1,
			'default' => 'n'
		)
	);
}
