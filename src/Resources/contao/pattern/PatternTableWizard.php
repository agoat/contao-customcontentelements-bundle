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

 
class PatternTableWizard extends \Pattern
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
				'style'			=>	'width:344px; height:72px'
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
		$strPreview = '<div class="" style="padding-top:10px;"><h3 style="margin: 0;"><label>' . $this->label . '</label> <a href="javascript:void(0);" title="" > <img src="system/themes/flexible/images/tablewizard.gif" alt="CSV import" style="vertical-align:text-bottom" height="14" width="16"></a> <img src="system/themes/flexible/images/demagnify.gif" alt="" title="" style="vertical-align:text-bottom;cursor:pointer" height="14" width="13"><img src="system/themes/flexible/images/magnify.gif" alt="" title="" style="vertical-align:text-bottom; cursor:pointer" height="14" width="13"></h3>';
		
		$strPreview .= '<table class="tl_tablewizard"><thead><tr><td style="text-align:center; white-space:nowrap"><a href="javascript:void(0);" title=""><img src="system/themes/flexible/images/copy.gif" alt="Duplicate the column" class="tl_tablewizard_img" height="16" width="14"></a> <a href="javascript:void(0);" title=""><img src="system/themes/flexible/images/movel.gif" alt="Move the column one position left" class="tl_tablewizard_img" height="16" width="13"></a> <a href="javascript:void(0);" title=""><img src="system/themes/flexible/images/mover.gif" alt="Move the column one position right" class="tl_tablewizard_img" height="16" width="13"></a> <a href="javascript:void(0);" title=""><img src="system/themes/flexible/images/delete.gif" alt="Delete the column" class="tl_tablewizard_img" height="16" width="14"></a></td><td style="text-align:center; white-space:nowrap"><a href="javascript:void(0);" title=""><img src="system/themes/flexible/images/copy.gif" alt="Duplicate the column" class="tl_tablewizard_img" height="16" width="14"></a> <a href="javascript:void(0);" title=""><img src="system/themes/flexible/images/movel.gif" alt="Move the column one position left" class="tl_tablewizard_img" height="16" width="13"></a> <a href="javascript:void(0);" title=""><img src="system/themes/flexible/images/mover.gif" alt="Move the column one position right" class="tl_tablewizard_img" height="16" width="13"></a> <a href="javascript:void(0);" title=""><img src="system/themes/flexible/images/delete.gif" alt="Delete the column" class="tl_tablewizard_img" height="16" width="14"></a></td><td></td></tr></thead><tbody class="sortable" data-tabindex="2"><tr><td class="tcontainer"><textarea name="tableItems_74_0[0][0]" class="tl_textarea noresize" tabindex="2" rows="12" cols="80" style="width:344px; height:72px"></textarea></td><td class="tcontainer"><textarea name="tableItems_74_0[0][1]" class="tl_textarea noresize" tabindex="3" rows="12" cols="80" style="width:344px; height:72px"></textarea></td><td style="white-space:nowrap"><a href="javascript:void(0);" title=""><img src="system/themes/flexible/images/copy.gif" alt="Duplicate the row" class="tl_tablewizard_img" height="16" width="14"></a> <img src="system/themes/flexible/images/drag.gif" alt="" class="drag-handle" title="" height="16" width="14"><a href="javascript:void(0);" class="button-move" title=""><img src="system/themes/flexible/images/up.gif" alt="Move the row one position up" class="tl_tablewizard_img" height="16" width="13"></a> <a href="javascript:void(0);" class="button-move" title=""><img src="system/themes/flexible/images/down.gif" alt="Move the row one position down" class="tl_tablewizard_img" height="16" width="13"></a> <a href="javascript:void(0);" title=""><img src="system/themes/flexible/images/delete.gif" alt="Delete the row" class="tl_tablewizard_img" height="16" width="14"></a> </td></tr><tr><td class="tcontainer"><textarea name="tableItems_74_0[0][0]" class="tl_textarea noresize" tabindex="2" rows="12" cols="80" style="width:344px; height:72px"></textarea></td><td class="tcontainer"><textarea name="tableItems_74_0[0][1]" class="tl_textarea noresize" tabindex="3" rows="12" cols="80" style="width:344px; height:72px"></textarea></td><td style="white-space:nowrap"><a href="javascript:void(0);" title=""><img src="system/themes/flexible/images/copy.gif" alt="Duplicate the row" class="tl_tablewizard_img" height="16" width="14"></a> <img src="system/themes/flexible/images/drag.gif" alt="" class="drag-handle" title="" height="16" width="14"><a href="javascript:void(0);" class="button-move" title=""><img src="system/themes/flexible/images/up.gif" alt="Move the row one position up" class="tl_tablewizard_img" height="16" width="13"></a> <a href="javascript:void(0);" class="button-move" title=""><img src="system/themes/flexible/images/down.gif" alt="Move the row one position down" class="tl_tablewizard_img" height="16" width="13"></a> <a href="javascript:void(0);" title=""><img src="system/themes/flexible/images/delete.gif" alt="Delete the row" class="tl_tablewizard_img" height="16" width="14"></a> </td></tr></tbody></table>';
			
		$strPreview .= '<p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';	

		return $strPreview;
	}


	/**
	 * prepare data for the frontend template 
	 */
	public function compile()
	{
		
		$this->writeToTemplate(deserialize($this->Value->tableItems));
		
	}
	
}
