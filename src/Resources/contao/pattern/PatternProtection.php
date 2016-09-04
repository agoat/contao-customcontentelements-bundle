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

 
class PatternProtection extends \Pattern
{


	/**
	 * generate the DCA construct
	 */
	public function construct()
	{
		// elements field so don´t use parent construct method
		$GLOBALS['TL_DCA']['tl_content']['palettes'][$this->alias] .= ',protected';

		// the groups field
		if ($this->canChangeGuests)
		{
			$GLOBALS['TL_DCA']['tl_content']['palettes'][$this->alias] .= ',guests';
		}
		// the groups field
		if (!$this->canChangeGroups)
		{
			if(($key = array_search('protected', $GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'])) !== false) {
				unset($GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][$key]);
			}
			unset($GLOBALS['TL_DCA']['tl_content']['subpalettes']['protected']);
			$GLOBALS['TL_DCA']['tl_content']['fields']['protected']['eval']['submitOnChange'] = false;
			$GLOBALS['TL_DCA']['tl_content']['fields']['protected']['eval']['class'] = 'clr';
	
			// also copy pattern groups to content element groups field
			$this->import("Database");
			$this->Database->prepare("UPDATE tl_content SET groups=? WHERE id=?")
						   ->execute($this->groups, $this->cid);
		}
	
	}
	

	/**
	 * Generate backend output
	 */
	public function view()
	{
		$strPreview = '<div id="ctrl_protected" class="tl_checkbox_single_container"><input name="protected" value="" type="hidden"><input name="protected" id="opt_protected_0" class="tl_checkbox" value="1" onfocus="Backend.getScrollOffset()" type="checkbox"' . (($this->canChangeGroups) ? 'checked' : '') . '> <label for="opt_protected_0">Protect element</label></div>';
		
		if ($this->canChangeGroups)
		{
			$strPreview .= '<fieldset id="ctrl_groups" class="tl_checkbox_container"><legend><span class="invisible">Mandatory field </span>Allowed member groups<span class="mandatory">*</span></legend><input name="groups" value="" type="hidden"><input id="check_all_groups" class="tl_checkbox" type="checkbox"> <label for="check_all_groups" style="color:#a6a6a6"><em>Select all</em></label><br><input name="groups[]" id="opt_groups_0" class="tl_checkbox" value="1" checked onfocus="Backend.getScrollOffset()" type="checkbox"> <label for="opt_groups_0">group1</label><br><input name="groups[]" id="opt_groups_1" class="tl_checkbox" value="2" onfocus="Backend.getScrollOffset()" type="checkbox"> <label for="opt_groups_1">group2</label></fieldset>';				
		}
		
		return $strPreview ;
	}


	/**
	 * Generate data for the frontend template 
	 */
	public function compile()
	{
		
		return;		
		
	}


	
}
