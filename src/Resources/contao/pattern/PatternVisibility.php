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


namespace Agoat;

 
class PatternVisibility extends \Pattern
{


	/**
	 * generate the DCA construct
	 */
	public function construct()
	{
		// elements field so don´t use parent construct method
		$GLOBALS['TL_DCA']['tl_content']['palettes'][$this->alias] .= ',invisible';
		$GLOBALS['TL_DCA']['tl_content']['fields']['invisible']['eval']['tl_class'] = 'clr'; // push to new row (clear)
	
		// the start field
		if ($this->canChangeStart)
		{
			$GLOBALS['TL_DCA']['tl_content']['palettes'][$this->alias] .= ',start';
		}
		// the stop field
		if ($this->canChangeStop)
		{
			$GLOBALS['TL_DCA']['tl_content']['palettes'][$this->alias] .= ',stop';
		}		
	}
	

	/**
	 * Generate backend output
	 */
	public function view()
	{
		$strPreview = '<div class="tl_checkbox_single_container"><input class="tl_checkbox" value="1" type="checkbox"> <label>Invisible</label><p title="" class="tl_help tl_tip">Hide the element on the website.</p></div>';	
		
		if ($this->canChangeStart)
		{
			$strPreview .= '<div class="w50 wizard" style="padding-top:10px;"><h3 style="margin: 0;"><label>' . $GLOBALS['TL_LANG']['tl_content_pattern']['start'][0] . '</label></h3>';
			$strPreview .= '<input class="tl_text" value="" type="text"> ';
			$strPreview .= '<img src="assets/mootools/datepicker/2.2.0/icon.gif" alt="Page picker" style="vertical-align:-6px;cursor:pointer" height="20" width="20">';
			$strPreview .= '<p title="" class="tl_help tl_tip">' . $GLOBALS['TL_LANG']['tl_content_pattern']['start'][1] . '</p></div>';			
		}

		if ($this->canChangeStop)
		{
			$strPreview .= '<div class="w50 wizard" style="padding-top:10px;"><h3 style="margin: 0;"><label>' . $GLOBALS['TL_LANG']['tl_content_pattern']['stop'][0] . '</label></h3>';
			$strPreview .= '<input class="tl_text" value="" type="text"> ';
			$strPreview .= '<img src="assets/mootools/datepicker/2.2.0/icon.gif" alt="Page picker" style="vertical-align:-6px;cursor:pointer" height="20" width="20">';
			$strPreview .= '<p title="" class="tl_help tl_tip">' . $GLOBALS['TL_LANG']['tl_content_pattern']['stop'][1] . '</p></div>';	
		}
		
		return $strPreview;
	}

	/**
	 * Generate data for the frontend template 
	 */
	public function compile()
	{
		// nothing to compile
		return;
		
	}


	
}
