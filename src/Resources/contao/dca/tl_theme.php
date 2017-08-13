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


// content block edit button
array_insert($GLOBALS['TL_DCA']['tl_theme']['list']['operations'], 3, array
(
	'ctb' => array
	(
		'label'               => &$GLOBALS['TL_LANG']['tl_theme']['ctb'],
		'href'                => 'table=tl_elements',
		'icon'                => 'bundles/agoatcontentblocks/contentblocks.svg',
		//'button_callback'     => array('tl_theme', 'editCss')
	)
));

// allow tl_content_blocks table
$GLOBALS['TL_DCA']['tl_theme']['config']['ctable'][] = 'tl_elements';

