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

use Contao\System;
use Contao\StringUtil;


class PatternImageSize extends Pattern
{


	/**
	 * generate the DCA construct
	 */
	public function construct()
	{
		// no input fields
	}
	

	/**
	 * Generate backend output
	 */
	public function view()
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
	 * Generate data for the frontend template 
	 */
	public function compile()
	{
		$this->writeToTemplate($this->size);
	}

	
}
