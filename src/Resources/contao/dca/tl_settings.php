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
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] = str_replace('{frontend_legend}', '{elements_legend},hideLegacyCTE,disableVisualSelect;{frontend_legend}', $GLOBALS['TL_DCA']['tl_settings']['palettes']['default']);


// Fields
$GLOBALS['TL_DCA']['tl_settings']['fields']['hideLegacyCTE'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['hideLegacyCTE'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50'),
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['disableVisualSelect'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['disableVisualSelect'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50'),
);
