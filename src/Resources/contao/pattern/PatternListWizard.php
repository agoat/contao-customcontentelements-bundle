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

use Agoat\ContentBlocks\Pattern;


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
		$strPreview = '<div class="" style="padding-top:10px;"><h3 style="margin: 0;"><label>' . $this->label . '</label> <a href="javascript:void(0);" title="" > <img src="system/themes/flexible/images/tablewizard.gif" alt="CSV import" style="vertical-align:text-bottom" height="14" width="16"></a></h3>';
		
		$strPreview .= '<ul class="tl_listwizard" data-tabindex="1"><li><input type="text" class="tl_text" tabindex="1" value=""> <a href="javascript:void(0);"><img src="system/themes/flexible/images/copy.gif" width="14" height="16" alt="Duplicate the element" class="tl_listwizard_img"></a> <a href="javascript:void(0);"><img src="system/themes/flexible/images/drag.gif" width="14" height="16" alt="" class="drag-handle" title="Move the item via drag and drop"></a> <a href="javascript:void(0);"><img src="system/themes/flexible/images/delete.gif" width="14" height="16" alt="Delete the element" class="tl_listwizard_img"></a></li><li><input type="text" class="tl_text" tabindex="1" value=""> <a href="javascript:void(0);"><img src="system/themes/flexible/images/copy.gif" width="14" height="16" alt="Duplicate the element" class="tl_listwizard_img"></a> <a href="javascript:void(0);"><img src="system/themes/flexible/images/drag.gif" width="14" height="16" alt="" class="drag-handle" title="Move the item via drag and drop"></a> <a href="javascript:void(0);"><img src="system/themes/flexible/images/delete.gif" width="14" height="16" alt="Delete the element" class="tl_listwizard_img"></a></li><li><input type="text" class="tl_text" tabindex="1" value=""> <a href="javascript:void(0);"><img src="system/themes/flexible/images/copy.gif" width="14" height="16" alt="Duplicate the element" class="tl_listwizard_img"></a> <a href="javascript:void(0);"><img src="system/themes/flexible/images/drag.gif" width="14" height="16" alt="" class="drag-handle" title="Move the item via drag and drop"></a> <a href="javascript:void(0);"><img src="system/themes/flexible/images/delete.gif" width="14" height="16" alt="Delete the element" class="tl_listwizard_img"></a></li></ul>';
			
		$strPreview .= '<p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';	

		return $strPreview;
	}


	/**
	 * prepare data for the frontend template 
	 */
	public function compile()
	{
		
		$this->writeToTemplate(deserialize($this->Value->listItems));
		
	}
	
}
