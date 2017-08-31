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


class PatternTableWizard extends Pattern
{
	/**
	 * generate the DCA construct
	 */
	public function construct()
	{
		$this->generateDCA('tableItems', array
		(
			'inputType' 	=>	'tableWizard',
			'label'			=>	array($this->label, $this->description),
			'eval'			=>	array
			(
				'mandatory'		=>	($this->mandatory) ? true : false, 
				'allowHtml'		=>	true,
				'style'			=>	'width:142px;height:66px'
			),
			'xlabel' => array
			(
				array('tl_content', 'tableImportWizard')
			),
		));
	}
	

	/**
	 * Generate backend output
	 */
	public function view()
	{
		$strPreview = '<div class="widget" style="padding-top:10px;"><h3 style="margin: 0;"><label>' . $this->label . '</label> <a href="javascript:void(0);" title="" > <img src="system/themes/flexible/icons/tablewizard.svg" alt="CSV import" style="vertical-align:text-bottom" height="16" width="16"></a> <img src="system/themes/flexible/icons/demagnify.svg" alt="" title="" style="vertical-align:text-bottom;cursor:pointer" height="16" width="16"><img src="system/themes/flexible/icons/magnify.svg" alt="" title="" style="vertical-align:text-bottom; cursor:pointer" height="16" width="16"></h3>';
		
		$strPreview .= '<table class="tl_tablewizard"><thead><tr><td style="text-align:center; white-space:nowrap"><a href="javascript:void(0);" title=""><img src="system/themes/flexible/icons/copy.svg" alt="Duplicate the column" class="tl_tablewizard_img" height="16" width="16"></a> <a href="javascript:void(0);" title=""><img src="system/themes/flexible/icons/movel.svg" alt="Move the column one position left" class="tl_tablewizard_img" height="16" width="16"></a> <a href="javascript:void(0);" title=""><img src="system/themes/flexible/icons/mover.svg" alt="Move the column one position right" class="tl_tablewizard_img" height="16" width="16"></a> <a href="javascript:void(0);" title=""><img src="system/themes/flexible/icons/delete.svg" alt="Delete the column" class="tl_tablewizard_img" height="16" width="16"></a></td><td style="text-align:center; white-space:nowrap"><a href="javascript:void(0);" title=""><img src="system/themes/flexible/icons/copy.svg" alt="Duplicate the column" class="tl_tablewizard_img" height="16" width="16"></a> <a href="javascript:void(0);" title=""><img src="system/themes/flexible/icons/movel.svg" alt="Move the column one position left" class="tl_tablewizard_img" height="16" width="16"></a> <a href="javascript:void(0);" title=""><img src="system/themes/flexible/icons/mover.svg" alt="Move the column one position right" class="tl_tablewizard_img" height="16" width="16"></a> <a href="javascript:void(0);" title=""><img src="system/themes/flexible/icons/delete.svg" alt="Delete the column" class="tl_tablewizard_img" height="16" width="16"></a></td><td></td></tr></thead><tbody class="sortable" data-tabindex="2">';

		$strRowPreview .= '<tr><td class="tcontainer"><textarea name="tableItems[0][0]" class="tl_textarea noresize" tabindex="2" rows="12" cols="80" style="width:142px;height:66px"></textarea></td><td class="tcontainer"><textarea name="tableItems[0][1]" class="tl_textarea noresize" tabindex="3" rows="12" cols="80" style="width:142px;height:66px"></textarea></td><td style="white-space:nowrap"><a href="javascript:void(0);" title=""><img src="system/themes/flexible/icons/copy.svg" alt="Duplicate the row" class="tl_tablewizard_img" height="16" width="16"></a> <img src="system/themes/flexible/icons/drag.svg" alt="" class="drag-handle" title="" height="16" width="16"> <a href="javascript:void(0);" title=""><img src="system/themes/flexible/icons/delete.svg" alt="Delete the row" class="tl_tablewizard_img" height="16" width="16"></a> </td></tr>';
			
		// Add two rows
		$strPreview .= $strRowPreview;	
		$strPreview .= $strRowPreview;	
		
		$strPreview .= '</tbody></table><p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';	

		return $strPreview;
	}


	/**
	 * prepare data for the frontend template 
	 */
	public function compile()
	{
		$this->writeToTemplate(StringUtil::deserialize($this->data->tableItems));
	}
	
}
