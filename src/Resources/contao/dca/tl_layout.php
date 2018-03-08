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


// Palettes
$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] .= ';{backend_legend:hide},backendCSS,backendJS';


// Fields
$GLOBALS['TL_DCA']['tl_layout']['fields']['backendCSS'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['backendCSS'],
	'exclude'                 => true,
	'inputType'               => 'fileTree',
	'eval'                    => array('multiple'=>true, 'orderField'=>'orderBackendCSS', 'fieldType'=>'checkbox', 'filesOnly'=>true, 'extensions'=>'css,scss,less'),
	'sql'                     => "blob NULL"
);
$GLOBALS['TL_DCA']['tl_layout']['fields']['orderBackendCSS'] = array
(
	'sql'                     => "blob NULL"
);
$GLOBALS['TL_DCA']['tl_layout']['fields']['backendJS'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['backendJS'],
	'exclude'                 => true,
	'inputType'               => 'fileTree',
	'eval'                    => array('multiple'=>true, 'orderField'=>'orderBackendJS', 'fieldType'=>'checkbox', 'filesOnly'=>true, 'extensions'=>'js'),
	'sql'                     => "blob NULL"
);
$GLOBALS['TL_DCA']['tl_layout']['fields']['orderBackendJS'] = array
(
	'sql'                     => "blob NULL"
);

