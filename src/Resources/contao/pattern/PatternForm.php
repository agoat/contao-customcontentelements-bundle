<?php

/*
 * Custom content elements extension for Contao Open Source CMS.
 *
 * @copyright  Arne Stappen (alias aGoat) 2017
 * @package    contao-contentelements
 * @author     Arne Stappen <mehh@agoat.xyz>
 * @link       https://agoat.xyz
 * @license    LGPL-3.0
 */

namespace Agoat\ContentElements;

use Contao\Form;


/**
 * Content element pattern "form"
 */
class PatternForm extends Pattern
{
	/**
	 * Creates the DCA configuration
	 */
	public function create()
	{
		return; // Nothing to set
	}
	

	/**
	 * Generate the pattern preview
	 *
	 * @return string HTML code
	 */
	public function preview()
	{
		$objForm = new Form($this);
		
		return '<div class="widget"><span style="color:#b3b3b3 ">' . $objForm->title . ' (ID ' . $objForm->id . ')</span></div>';
	}


	/**
	 * Prepare the data for the template
	 */
	public function compile()
	{
		// Call the form class
		$objForm = new Form($this);
		$objForm->formTemplate = $this->formTemplate;
		
		$this->writeToTemplate($objForm->generate());		
	}
}
