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

use Contao\StringUtil;


/**
 * Content element pattern "explanation"
 */
class PatternExplanation extends Pattern
{
	/**
	 * Creates the DCA configuration
	 */
	public function create()
	{
		// Set id as pattern as there is no alias
		$this->pattern = $this->id;
	
		// An explanation field
		$this->generateDCA('explanation', array
		(
			'inputType' =>	'explanation',
			'eval'		=>	array
			(
				'explanation'	=>	StringUtil::toHtml5($this->explanation), 
				'tl_class'		=>	'clr'
			)
		), true, false);
	}


	/**
	 * Generate the pattern preview
	 *
	 * @return string HTML code
	 */
	public function preview()
	{
		return '<div class="widget clr"><div class="tl_explanation">' . StringUtil::toHtml5($this->explanation) . '</div></div>';
	}


	/**
	 * Prepare the data for the template
	 */
	public function compile()
	{
		return; // Nothing to show in the frontend
	}
}
