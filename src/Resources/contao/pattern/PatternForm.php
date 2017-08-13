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

use Contao\Form;


class PatternForm extends Pattern
{


	/**
	 * generate the DCA construct
	 */
	public function construct()
	{
		// nothing to select
		return;
	}
	

	/**
	 * Generate backend output
	 */
	public function view()
	{
		$objForm = new Form($this);
		return '<span>' . $objForm->title . ' (ID ' . $objForm->id . ')</span>';
	}

	/**
	 * Generate data for the frontend template 
	 */
	public function compile()
	{
		// call the form class
		$objForm = new Form($this);
		$objForm->formTemplate = $this->formTemplate;
		
		$this->writeToTemplate($objForm->generate());		
	}


	
}
