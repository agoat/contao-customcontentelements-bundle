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


class PatternPageTree extends Pattern
{


	/**
	 * generate the DCA construct
	 */
	public function construct()
	{
		
		$this->generateDCA('pageSRC', array
		(
			'inputType' 	=>	'pageTree',
			'label'			=>	array($this->label, $this->description),
			'foreignKey'    => 'tl_page.title',
			'eval'			=>	array
			(
				'mandatory'		=>	($this->mandatory) ? true : false, 
				'tl_class'		=> 	'clr',
				'fieldType'		=>	'radio', 
			)
		));
		
	}
	

	/**
	 * Generate backend output
	 */
	public function view()
	{
		$strPreview = '<div class="inline" style="padding-top:10px;"><h3 style="margin: 0;"><label>' . $this->label . '</label></h3><div class="selector_container"><ul>';
		$strPreview .= '<li><img src="system/themes/flexible/icons/regular.svg" width="18" height="18" alt=""> Pagetitle</li>';				
		$strPreview .= '</ul><p><a href="javascript:void(0);" class="tl_submit">Change selection</a></p></div><p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';

		return $strPreview;
	}


	/**
	 * prepare data for the frontend template 
	 */
	public function compile()
	{
		// prepare value(s)
		$this->writeToTemplate($this->Value->pageSRC);
	}
	
}
