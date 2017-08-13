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


class PatternListWizard extends Pattern
{
	/**
	 * generate the DCA construct
	 */
	public function construct()
	{
		$this->generateDCA('listItems', array
		(
			'inputType' 	=>	'listWizard',
			'label'			=>	array($this->label, $this->description),
			'eval'			=>	array
			(
				'mandatory'		=>	($this->mandatory) ? true : false, 
				'allowHtml'		=>	true,
				'tl_class'		=>	'clr'
			),
			'xlabel' => array
			(
				array('tl_content', 'listImportWizard')
			),

		));
	}
	

	/**
	 * Generate backend output
	 */
	public function view()
	{
		$strPreview = '<div class="" style="padding-top:10px;"><h3 style="margin: 0;"><label>' . $this->label . '</label> <a href="javascript:void(0);" title="" > <img src="system/themes/flexible/icons/tablewizard.svg" alt="CSV import" style="vertical-align:text-bottom" height="14" width="16"></a></h3>';
		
		$strRowPreview .= '<ul class="tl_listwizard" data-tabindex="1"><li><input type="text" class="tl_text" tabindex="1" value=""> <a href="javascript:void(0);"><img src="system/themes/flexible/icons/copy.svg" width="14" height="16" alt="Duplicate the element" class="tl_listwizard_img"></a> <a href="javascript:void(0);"><img src="system/themes/flexible/icons/drag.svg" width="14" height="16" alt="" class="drag-handle" title="Move the item via drag and drop"></a> <a href="javascript:void(0);"><img src="system/themes/flexible/icons/delete.svg" width="14" height="16" alt="Delete the element" class="tl_listwizard_img"></a></li></ul>';
			
		// Add three exmaple lines
		$strPreview .= $strRowPreview;
		$strPreview .= $strRowPreview;
		$strPreview .= $strRowPreview;
		
		$strPreview .= '<p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';	

		return $strPreview;
	}


	/**
	 * prepare data for the frontend template 
	 */
	public function compile()
	{
		$this->writeToTemplate(StringUtil::deserialize($this->Value->listItems));
	}
	
}
