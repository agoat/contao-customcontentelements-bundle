<?php
 
 /**
 * Contao Open Source CMS - ContentBlocks extension
 *
 * Copyright (c) 2016 Arne Stappen (aGoat)
 *
 *
 * @package   contentblocks
 * @author    Arne Stappen <http://agoat.de>
 * @license	  LGPL-3.0+
 */


namespace Contao;

 
class PatternExplanation extends \Pattern
{

	
	/**
	 * generate the DCA construct
	 */
	public function construct()
	{
		
		// an explanation field

		$this->generateDCA('explanation', array
		(
			'inputType' =>	'explanation',
			'eval'		=>	array
			(
				'explanation'	=>	\StringUtil::toHtml5($this->explanation), 
			)
		), false);
	
	}


	/**
	 * prepare a field view for the backend
	 *
	 * @param array $arrAttributes An optional attributes array
	 */
	public function view()
	{
		return '<div style="padding-top:10px;">' . \StringUtil::toHtml5($this->explanation) . '</div>';
	}


	/**
	 * prepare the values for the frontend template
	 *
	 * @param array $arrAttributes An optional attributes array
	 */	
	public function compile()
	{
		return;
	}
}
