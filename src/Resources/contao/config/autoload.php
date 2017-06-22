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



/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'cb_standard' => 'vendor/agoat/contentblocks-bundle/src/Resources/contao/templates',
	'cb_simple' => 'vendor/agoat/contentblocks-bundle/src/Resources/contao/templates',
	'cb_debug' => 'vendor/agoat/contentblocks-bundle/src/Resources/contao/templates',
	'be_tinyMCE_simple' => 'vendor/agoat/contentblocks-bundle/src/Resources/contao/templates',
	'be_tinyExplanation' => 'vendor/agoat/contentblocks-bundle/src/Resources/contao/templates',
));

