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

use Contao\Database;


/**
 * Content element pattern "protection"
 */
class PatternProtection extends Pattern
{
	/**
	 * Creates the DCA configuration
	 */
	public function create()
	{
		// Element fields, so don´t use parent construct method
		$GLOBALS['TL_DCA']['tl_content']['palettes'][$this->element] .= ',protected';
		$GLOBALS['TL_DCA']['tl_content']['fields']['protected']['eval']['tl_class'] = 'clr'; // push to new row (clear)

		// The groups field
		if (!$this->canChangeGroups)
		{
			if(($key = array_search('protected', $GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'])) !== false) {
				unset($GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][$key]);
			}
			
			unset($GLOBALS['TL_DCA']['tl_content']['subpalettes']['protected']);
			
			$GLOBALS['TL_DCA']['tl_content']['fields']['protected']['eval']['submitOnChange'] = false;
			
			// Overwrite the elements groups with groups from the pattern
			$db = Database::getInstance();
			$db->prepare("UPDATE tl_content SET groups=? WHERE id=?")
			   ->execute($this->groups, $this->cid);
		}
		
		// The guests field
		if ($this->canChangeGuests)
		{
			$GLOBALS['TL_DCA']['tl_content']['palettes'][$this->element] .= ',guests';
		}
	}
	

	/**
	 * Generate the pattern preview
	 *
	 * @return string HTML code
	 */
	public function preview()
	{
		$strPreview = '<div class="widget clr"><div id="ctrl_protected" class="tl_checkbox_single_container"><input name="protected" value="1" type="hidden"><input name="protected" id="opt_protected_0" class="tl_checkbox" value="1" onfocus="Backend.getScrollOffset()" type="checkbox"' . (($this->canChangeGroups) ? 'checked' : '') . '> <label for="opt_protected_0">' . $GLOBALS['TL_LANG']['tl_pattern']['protected'][0] . '</label><p class="tl_help tl_tip" title="">' . $GLOBALS['TL_LANG']['tl_pattern']['protected'][1] . '</p></div></div>';
		
		if ($this->canChangeGroups)
		{
			$strPreview .= '<div class="widget"><fieldset id="ctrl_groups" class="tl_checkbox_container"><legend><span class="invisible">Mandatory field </span>Allowed member groups<span class="mandatory">*</span></legend><input name="groups" value="" type="hidden"><input id="check_all_groups" class="tl_checkbox" type="checkbox"> <label for="check_all_groups" style="color:#a6a6a6"><em>Select all</em></label><br><input name="groups[]" id="opt_groups_0" class="tl_checkbox" value="1" checked onfocus="Backend.getScrollOffset()" type="checkbox"> <label for="opt_groups_0">group1</label><br><input name="groups[]" id="opt_groups_1" class="tl_checkbox" value="2" onfocus="Backend.getScrollOffset()" type="checkbox"> <label for="opt_groups_1">group2</label></fieldset></div>';				
		}
		
		return $strPreview ;
	}


	/**
	 * Prepare the data for the template
	 */
	public function compile()
	{
		return; // Nothing to show in the frontend
	}
}
