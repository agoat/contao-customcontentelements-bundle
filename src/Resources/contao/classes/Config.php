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


/**
 * Configuration class
 */
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

