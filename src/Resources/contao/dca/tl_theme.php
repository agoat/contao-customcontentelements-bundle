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

 
// Add content element edit button
array_insert($GLOBALS['TL_DCA']['tl_theme']['list']['operations'], 3, array
(
	'ctb' => array
	(
		'label'               => &$GLOBALS['TL_LANG']['tl_theme']['cte'],
		'href'                => 'table=tl_elements',
		'icon'                => 'bundles/agoatcustomcontentelements/contentblocks.svg',
		//'button_callback'     => array('tl_theme', 'editCss')
	)
));


// Add tl_elements table
$GLOBALS['TL_DCA']['tl_theme']['config']['ctable'][] = 'tl_elements';
