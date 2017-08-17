<?php

/*
 * This file is part of the ContentBlocks Extension.
 *
 * Copyright (c) 2016 Arne Stappen (alias aGoat)
 *
 * @license LGPL-3.0+
 */

namespace Agoat\ContentElementsBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Adds the bundle services to the container.
 *
 * @author Arne Stappen <https://github.com/agoat>
 */
class AgoatContentElementsExtension extends Extension
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
