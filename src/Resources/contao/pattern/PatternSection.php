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

 
class PatternSection extends \Pattern
{

	
	/**
	 * generate the DCA construct
	 */
	public function construct()
	{

		$GLOBALS['TL_DCA']['tl_content']['palettes'][$this->alias] .= ';{section_'.$this->id . (($this->hidden) ? ':hide' : ''). '}';
		$GLOBALS['TL_LANG']['tl_content']['section_'.$this->id] = $this->label;

	}


	/**
	 * prepare a field view for the backend
	 *
	 * @param array $arrAttributes An optional attributes array
	 */
	public function view()
	{
		return '<div style="padding-top:20px;"><fieldset id="pal_section_' . $this->id . '" class="tl_box" style="padding-bottom: 10px; border-width: 2px 0 0;"><legend>' . $this->label . '</legend></fieldset></div>';
	}


	/**
	 * prepare the values for the frontend template
	 *
	 * @param array $arrAttributes An optional attributes array
	 */	
	public function compile()
	{
		return;
	}
}
