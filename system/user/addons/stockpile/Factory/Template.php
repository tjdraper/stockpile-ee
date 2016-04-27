<?php

namespace BuzzingPixel\Stockpile\Factory;

class Template
{
	/**
	 * Get an EE template model
	 *
	 * @param string $template template-group/template
	 * @return object Template model
	 */
	public static function get($template)
	{
		// Get the template parts
		$templateParts = explode('/', $template);

		// Check to make sure everything is in order
		if (count($templateParts) !== 2) {
			return null;
		}

		// Get the template group
		$templateGroup = ee('Model')->get('TemplateGroup')
			->fields('group_id')
			->filter('group_name', $templateParts[0])
			->first();

		// Make sure the template group exists
		if (! $templateGroup) {
			return null;
		}

		// Get the template
		$template = ee('Model')->get('Template')
			->filter('group_id', $templateGroup->group_id)
			->filter('template_name', $templateParts[1])
			->first();

		// Return the template
		return $template;
	}
}
