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
	
	
	/**
	 * Register callbacks for the news extension bundles
	 */
	public function setNewsArticleCallbacks ($strTable)
	{
		if ($strTable != 'tl_news' || TL_MODE == 'FE')
		{
			return;
		}
		
		$GLOBALS['TL_DCA']['tl_news']['config']['oncopy_callback'][] = array('tl_news_contentblocks', 'copyRelatedValues');
		$GLOBALS['TL_DCA']['tl_news']['config']['ondelete_callback'][] = array('tl_news_contentblocks', 'deleteRelatedValues');

	}

}

