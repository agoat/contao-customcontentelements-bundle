<?php

/*
 * Custom content elements extension for Contao Open Source CMS.
 *
 * @copyright  Arne Stappen (alias aGoat) 2017
 * @package    contao-contentelements
 * @author     Arne Stappen <mehh@agoat.xyz>
 * @link       https://agoat.xyz
 * @license    LGPL-3.0
 */

namespace Agoat\ContentElements;


/**
 * Content element pattern "visibility"
 */
class PatternVisibility extends Pattern
{
	/**
	 * Creates the DCA configuration
	 */
	public function create()
	{
		// Element fields, so don´t use parent construct method
		$GLOBALS['TL_DCA']['tl_content']['palettes'][$this->element] .= ',invisible';
		$GLOBALS['TL_DCA']['tl_content']['fields']['invisible']['eval']['tl_class'] = 'clr'; // push to new row (clear)
	
		// The start field
		if ($this->canChangeStart)
		{
			$GLOBALS['TL_DCA']['tl_content']['palettes'][$this->element] .= ',start';
		}
		// The stop field
		if ($this->canChangeStop)
		{
			$GLOBALS['TL_DCA']['tl_content']['palettes'][$this->element] .= ',stop';
		}		
	}
	

	/**
	 * Generate the pattern preview
	 *
	 * @return string HTML code
	 */
	public function preview()
	{
		$strPreview = '<div class="widget m12"><div class="tl_checkbox_single_container"><input class="tl_checkbox" value="1" type="checkbox"> <label>' . $GLOBALS['TL_LANG']['tl_pattern']['invisible'][0] . '</label><p title="" class="tl_help tl_tip">' . $GLOBALS['TL_LANG']['tl_pattern']['invisible'][1] . '</p></div></div>';	
		
		if ($this->canChangeStart)
		{
			$strPreview .= '<div class="w50 wizard widget" style="padding-top:10px;"><h3 style="margin: 0;"><label>' . $GLOBALS['TL_LANG']['tl_pattern']['start'][0] . '</label></h3>';
			$strPreview .= '<input class="tl_text" value="" type="text"> ';
			$strPreview .= '<img src="assets/datepicker/images/icon.svg" alt="Page picker" style="cursor:pointer" height="20" width="20">';
			$strPreview .= '<p title="" class="tl_help tl_tip">' . $GLOBALS['TL_LANG']['tl_pattern']['start'][1] . '</p></div>';			
		}

		if ($this->canChangeStop)
		{
			$strPreview .= '<div class="w50 wizard widget" style="padding-top:10px;"><h3 style="margin: 0;"><label>' . $GLOBALS['TL_LANG']['tl_pattern']['stop'][0] . '</label></h3>';
			$strPreview .= '<input class="tl_text" value="" type="text"> ';
			$strPreview .= '<img src="assets/datepicker/images/icon.svg" alt="Page picker" style="cursor:pointer" height="20" width="20">';
			$strPreview .= '<p title="" class="tl_help tl_tip">' . $GLOBALS['TL_LANG']['tl_pattern']['stop'][1] . '</p></div>';	
		}
		
		return $strPreview;
	}

	/**
	 * Prepare the data for the template
	 */
	public function compile()
	{
		return; // Nothing to show in the frontend
	}
}
