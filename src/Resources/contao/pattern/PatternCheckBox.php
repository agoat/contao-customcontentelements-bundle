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


class PatternCheckBox extends Pattern
{
	/**
	 * generate the DCA construct
	 */
	public function construct()
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
	 * Generate backend output
	 */
	public function view()
	{
		return '<div class="w50 widget m12"><div class="tl_checkbox_single_container"><input class="tl_checkbox" value="1" type="checkbox"> <label>' . $this->label . '</label><p title="" class="tl_help tl_tip">' . $this->description . '</p></div></div>';	
	}


	/**
	 * prepare data for the frontend template 
	 */
	public function compile()
	{
		
		$this->writeToTemplate(($this->data->checkBox) ? true : false);
		
	}
	
}
