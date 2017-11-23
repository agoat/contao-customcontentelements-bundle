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

namespace Agoat\CustomContentElementsBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;


/**
 * Adds the bundle services and paramters to the container.
 */
class AgoatCustomContentElementsExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
		
		// Set valid extensions paramater if not set anyway
		if (!$container->hasParameter('contao.video.valid_extensions'))
		{
			$container->setParameter('contao.video.valid_extensions', ['mp4', 'm4v', 'mov', 'wmv', 'webm', 'ogv']);
		}
		
		if (!$container->hasParameter('contao.audio.valid_extensions'))
		{
			$container->setParameter('contao.audio.valid_extensions', ['m4a', 'mp3', 'wma', 'mpeg', 'wav', 'ogg']);
		}		
    }
}
