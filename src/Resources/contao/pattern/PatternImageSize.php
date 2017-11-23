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

use Contao\System;
use Contao\StringUtil;


/**
 * Content element pattern "imagesize"
 */
class PatternImageSize extends Pattern
{
	/**
	 * Creates the DCA configuration
	 */
	public function create()
	{
		return; // Nothing to set
	}
	

	/**
	 * Generate the pattern preview
	 *
	 * @return string HTML code
	 */
	public function preview()
	{
		$sizes = \System::getContainer()->get('contao.image.image_sizes')->getAllOptions();
		$size = StringUtil::deserialize($this->size);
		
		if (is_numeric($size[2]))
		{
			return '<div class="widget"><span style="color:#b3b3b3 ">' . $sizes['image_sizes'][$size[2]] . '</span></div>';
		}
		else
		{
			return '<div class="widget"><span style="color:#b3b3b3 ">' . $GLOBALS['TL_LANG']['MSC'][$size[2]][0] . ' (' . $size[0] . 'x' . $size[1] . ')</span></div>';
		}
	}


	/**
	 * Prepare the data for the template
	 */
	public function compile()
	{
		$this->writeToTemplate($this->size);
	}
}
