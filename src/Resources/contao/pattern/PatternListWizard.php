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
 * Content element pattern "listwizard"
 */
class PatternListWizard extends Pattern
{
	/**
	 * Creates the DCA configuration
	 */
	public function create()
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
	 * Generate the pattern preview
	 *
	 * @return string HTML code
	 */
	public function preview()
	{
		$strPreview = '<div class="widget clr" style="padding-top:10px;"><h3 style="margin: 0;"><label>' . $this->label . '</label> <a href="javascript:void(0);" title="" > <img src="system/themes/flexible/icons/tablewizard.svg" alt="CSV import" style="vertical-align:text-bottom" height="14" width="16"></a></h3>';
		
		$strRowPreview = '<ul class="tl_listwizard" data-tabindex="1"><li><input type="text" class="tl_text" tabindex="1" value=""> <a href="javascript:void(0);"><img src="system/themes/flexible/icons/copy.svg" width="14" height="16" alt="Duplicate the element" class="tl_listwizard_img"></a> <a href="javascript:void(0);"><img src="system/themes/flexible/icons/drag.svg" width="14" height="16" alt="" class="drag-handle" title="Move the item via drag and drop"></a> <a href="javascript:void(0);"><img src="system/themes/flexible/icons/delete.svg" width="14" height="16" alt="Delete the element" class="tl_listwizard_img"></a></li></ul>';
			
		// Show three example lines
		$strPreview .= $strRowPreview;
		$strPreview .= $strRowPreview;
		$strPreview .= $strRowPreview;
		
		$strPreview .= '<p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';	

		return $strPreview;
	}


	/**
	 * Prepare the data for the template
	 */
	public function compile()
	{
		$this->writeToTemplate(StringUtil::deserialize($this->data->listItems));
	}
}
