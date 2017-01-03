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

namespace Agoat\ContentBlocks;

use Contao\TemplateLoader;
use Agoat\ContentBlocks\Pattern;


class PatternCode extends Pattern
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
				'rte'			=>	'ace|' . strtolower($this->highlight),
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

		$this->selector = $selector = 'ctrl_textarea' . $this->id;
		$type = strtolower($this->highlight);

		$strPreview .= '<textarea id="' . $selector . '" aria-hidden="true" class="tl_textarea noresize" rows="12" cols="80"></textarea>';
		
		ob_start();
		include(TemplateLoader::getPath('be_ace', 'html5'));
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
		$this->writeToTemplate(array('code' => $this->Value->text, 'highlight' => $this->highlight));
	}
	
}
