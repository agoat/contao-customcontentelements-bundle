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


class PatternSection extends Pattern
{

	
	/**
	 * generate the DCA construct
	 */
	public function construct()
	{

		$GLOBALS['TL_DCA']['tl_content']['palettes'][$this->element] .= ';{section-'. $this->id . (($this->hidden) ? ':hide' : ''). '}';
		$GLOBALS['TL_LANG']['tl_content']['section-' . $this->id] = $this->label;

	}


	/**
	 * prepare a field view for the backend
	 *
	 * @param array $arrAttributes An optional attributes array
	 */
	public function view()
	{
		return '<div style="padding-top:10px;"><fieldset id="pal_section_' . $this->id . '" class="tl_box" style="padding-top: 0;"><legend>' . $this->label . '</legend></fieldset></div>';
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
