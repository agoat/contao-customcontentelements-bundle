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


class Config
{

	/**
	 * Push symfony configuration into the contao config array
	 */
	public function loadParameters()
	{
		$container = \System::getContainer();
	
		if ($container->hasParameter('contao.video.valid_extensions'))
		{
			$GLOBALS['TL_CONFIG']['validVideoTypes'] = implode(',', $container->getParameter('contao.video.valid_extensions'));
		}
		
		if ($container->hasParameter('contao.audio.valid_extensions'))
		{
			$GLOBALS['TL_CONFIG']['validAudioTypes'] = implode(',', $container->getParameter('contao.audio.valid_extensions'));
		}

	}
}

