<?php

/*
 * Custom content elements extension for Contao Open Source CMS.
 *
 * @copyright  Arne Stappen (alias aGoat) 2017
 * @package    contao-customcontentelements
 * @author     Arne Stappen <mehh@agoat.xyz>
 * @link       https://agoat.xyz
 * @license    LGPL-3.0
 */

namespace Agoat\CustomContentElementsBundle\Contao;


/**
 * Content element pattern "explanation"
 */
class PatternSection extends Pattern
{
	/**
	 * Creates the DCA configuration
	 */
	public function create()
	{

		$GLOBALS['TL_DCA']['tl_content']['palettes'][$this->element] .= ';{section-'. $this->id . (($this->hidden) ? ':hide' : ''). '}';
		$GLOBALS['TL_LANG']['tl_content']['section-' . $this->id] = $this->label;
	}


	/**
	 * Generate the pattern preview
	 *
	 * @return string HTML code
	 */
	public function preview()
	{
		return '<div style="padding-top:10px;"><fieldset id="pal_section_' . $this->id . '" class="tl_box" style="padding-top: 0;"><legend>' . $this->label . '</legend></fieldset></div>';
	}


	/**
	 * Prepare the data for the template
	 */
	public function compile()
	{
		return; // Nothing to show in the frontend
	}
}
