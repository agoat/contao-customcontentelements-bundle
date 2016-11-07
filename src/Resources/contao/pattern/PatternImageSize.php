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

namespace Agoat\ContentBlocks;

use Contao\System;
use Contao\StringUtil;
use Agoat\ContentBlocks\Pattern;


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
			return $sizes['image_sizes'][$size[2]];
		}
		else
		{
			return $GLOBALS['TL_LANG']['MSC'][$size[2]][0] . ' (' . $size[0] . 'x' . $size[1] . ')';
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
