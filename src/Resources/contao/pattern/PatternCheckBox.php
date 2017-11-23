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
 * Content element pattern "checkbox"
 */
class PatternCheckBox extends Pattern
{
	/**
	 * Creates the DCA configuration
	 */
	public function create()
	{
		$class = ($this->classClr) ? 'w50 clr m12' : 'w50 m12';
	
		$this->generateDCA('checkBox', array
		(
			'inputType' 	=>	'checkbox',
			'label'			=>	array($this->label, $this->description),
			'eval'			=>	array
			(
				'mandatory'		=>	($this->mandatory) ? true : false, 
				'tl_class'		=>	$class,
			)
		));
	}
	

	/**
	 * Generate the pattern preview
	 *
	 * @return string HTML code
	 */
	public function preview()
	{
		return '<div class="w50 widget m12"><div class="tl_checkbox_single_container"><input class="tl_checkbox" value="1" type="checkbox"> <label>' . $this->label . '</label><p title="" class="tl_help tl_tip">' . $this->description . '</p></div></div>';	
	}


	/**
	 * Prepare the data for the template
	 */
	public function compile()
	{
		$this->writeToTemplate(($this->data->checkBox) ? true : false);
	}
}
