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

namespace Agoat\ContentElements;

use Contao\StringUtil;


class PatternExplanation extends Pattern
{

	
	/**
	 * generate the DCA construct
	 */
	public function construct()
	{
		// Set id as pattern as there is no alias
		$this->pattern = $this->id;
	
		// An explanation field
		$this->generateDCA('explanation', array
		(
			'inputType' =>	'explanation',
			'eval'		=>	array
			(
				'explanation'	=>	\StringUtil::toHtml5($this->explanation), 
				'tl_class'		=>	'clr'
			)
		), true, false);
	}


	/**
	 * prepare a field view for the backend
	 *
	 * @param array $arrAttributes An optional attributes array
	 */
	public function view()
	{
		return '<div class="widget"><div class="tl_explanation">' . \StringUtil::toHtml5($this->explanation) . '</div></div>';
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
