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

 
class PatternCode extends \Pattern
{


	/**
	 * generate the DCA construct
	 */
	public function construct()
	{
		
		$this->generateDCA('text', array
		(
			'inputType' 	=>	'textarea',
			'label'			=>	array($this->label, $this->description),
			'eval'			=>	array
			(
				'mandatory'		=>	($this->mandatory) ? true : false, 
				'tl_class'		=> 	'clr',
				'rte'			=>	'ace|'.strtolower($this->highlight),
				'preserveTags'	=>	true,
			)
		));
		
	}
	

	/**
	 * Generate backend output
	 */
	public function view()
	{
		$strPreview = '<div class="" style="padding-top:10px;"><h3 style="margin: 0;"><label>' . $this->label . '</label></h3>';

		$selector = 'ctrl_textarea' . $this->id;
		$type = strtolower($this->highlight);

		$strPreview .= '<textarea id="' . $selector . '" aria-hidden="true" class="tl_textarea noresize" rows="12" cols="80"></textarea>';
		
		ob_start();
		include TL_ROOT . '/system/config/ace.php';
		$strPreview .= ob_get_contents();
		ob_end_clean();
			
		$strPreview .= '<p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';	

		return $strPreview;
	}


	/**
	 * prepare data for the frontend template 
	 */
	public function compile()
	{
		// prepare value(s)
		
		$this->writeToTemplate($this->Value->text);
	}
	
}
