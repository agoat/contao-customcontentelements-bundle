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

 
/**
 * Register back end module (tables, css, overwritten classes)
 */
array_push($GLOBALS['BE_MOD']['design']['themes']['tables'], 'tl_elements', 'tl_pattern');


/**
 * Style sheet
 */
if (TL_MODE == 'BE')
{
	$GLOBALS['TL_CSS'][] = 'bundles/agoatcustomcontentelements/style.css|static';
	$GLOBALS['TL_JAVASCRIPT'][] = 'bundles/agoatcustomcontentelements/core.js';
}


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['getPageLayout'][] = array('Agoat\\CustomContentElementsBundle\\Contao\\Controller','registerBlockElements');
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('Agoat\\CustomContentElementsBundle\\Contao\\Controller','registerBlockElements');
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('Agoat\\CustomContentElementsBundle\\Contao\\Controller','handleSubPatternTable');

$GLOBALS['TL_HOOKS']['parseTemplate'][] = array('Agoat\\CustomContentElementsBundle\\Contao\\Controller','addPageLayoutToBE');

$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][] = array('Agoat\\CustomContentElementsBundle\\Contao\\Controller','addContentElementsCSS');
$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][] = array('Agoat\\CustomContentElementsBundle\\Contao\\Controller','addContentElementsJS');

$GLOBALS['TL_HOOKS']['parseTemplate'][] = array('Agoat\\CustomContentElementsBundle\\Contao\\Versions','hideDataTableVersions');

$GLOBALS['TL_HOOKS']['executePostActions'][] = array('Agoat\\CustomContentElementsBundle\\Contao\\Ajax','executeGroupActions');

$GLOBALS['TL_HOOKS']['compareThemeFiles'][] = array('Agoat\\CustomContentElementsBundle\\Contao\\Theme','compareTables');
$GLOBALS['TL_HOOKS']['extractThemeFiles'][] = array('Agoat\\CustomContentElementsBundle\\Contao\\Theme','importTables');
$GLOBALS['TL_HOOKS']['exportTheme'][] = array('Agoat\\CustomContentElementsBundle\\Contao\\Theme','exportTables');

$GLOBALS['TL_HOOKS']['initializeSystem'][] = array('Agoat\\CustomContentElementsBundle\\Contao\\Config','loadParameters');
 

/**
 * Pattern elements
 */
$GLOBALS['TL_CTP'] = array
(
	'input' => array
	(
		'textfield'		=> array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternTextField',
			'data'			=> true,
			'output'		=> true,
			'childOf'		=> array('subpattern', 'multipattern'),
		),
		'textarea'		=> array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternTextArea',
			'data'			=> true,
			'output'		=> true,
			'childOf'		=> array('subpattern', 'multipattern'),
		),
		'selectfield'	=> array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternSelectField',
			'data'			=> true,
			'output'		=> true,
			'childOf'		=> array('subpattern', 'multipattern'),
		),
		'checkbox'		=> array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternCheckBox',
			'data'			=> true,
			'output'		=> true,
			'childOf'		=> array('subpattern', 'multipattern'),
		),
		'filetree'		=> array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternFileTree',
			'data'			=> true,
			'output'		=> true,
			'childOf'		=> array('subpattern', 'multipattern'),
		),
		'pagetree'		=> array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternPageTree',
			'data'			=> true,
			'output'		=> true,
			'childOf'		=> array('subpattern', 'multipattern'),
		),
		'listwizard'	=> array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternListWizard',
			'data'			=> true,
			'output'		=> true,
			'childOf'		=> array('subpattern', 'multipattern'),
		),
		'tablewizard'	=> array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternTableWizard',
			'data'			=> true,
			'output'		=> true,
			'childOf'		=> array('subpattern', 'multipattern'),
		),
		'code'			=> array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternCode',
			'data'			=> true,
			'output'		=> true,
			'childOf'		=> array('subpattern', 'multipattern'),
		)
	),
	'layout' => array
	(
		'section' => array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternSection',
		),
		'explanation' => array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternExplanation',
			'childOf'		=> array('subpattern', 'multipattern'),
		),
		'subpattern' => array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternSubPattern',
			'data'			=> true,
			'output'		=> true,
			'subpattern'	=> true,
			'childOf'		=> array('subpattern', 'multipattern'),
		),
		'multipattern' => array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternMultiPattern',
			'data'			=> true,
			'output'		=> true,
			'subpattern'	=> true,
			'childOf'		=> array('subpattern', 'multipattern'),
		),
	),
	'element' => array
	(
		'visibility' => array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternVisibility',
			'unique'		=> true,
			'output'		=> true,
		),
		'protection' => array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternProtection',
			'unique'		=> true,
			'output'		=> true,
		),
	),
	'system' => array
	(
		'imagesize' => array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternImageSize',
			'output'		=> true,
			'childOf'		=> array('subpattern'),
		),
		'form' => array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternForm',
			'unique'		=> true,
			'output'		=> true,
		),
		'module' => array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternModule',
			'unique'		=> true,
			'output'		=> true,
		)
	)
);


/**
 * Back end form fields (widgets)
 */
$GLOBALS['BE_FFL']['explanation'] 	= '\Agoat\CustomContentElementsBundle\Contao\Explanation';
$GLOBALS['BE_FFL']['visualselect'] 	= '\Agoat\CustomContentElementsBundle\Contao\VisualSelectMenu';
$GLOBALS['BE_FFL']['fileTree'] 		= '\Agoat\CustomContentElementsBundle\Contao\FileTree';
$GLOBALS['BE_FFL']['pageTree'] 		= '\Agoat\CustomContentElementsBundle\Contao\PageTree';
$GLOBALS['BE_FFL']['articleTree'] 	= '\Agoat\CustomContentElementsBundle\Contao\ArticleTree';

$GLOBALS['BE_FFL']['group'] 		= '\Agoat\CustomContentElementsBundle\Contao\Group';
$GLOBALS['BE_FFL']['groupstart'] 	= '\Agoat\CustomContentElementsBundle\Contao\GroupStart';
$GLOBALS['BE_FFL']['groupstop'] 	= '\Agoat\CustomContentElementsBundle\Contao\GroupStop';
$GLOBALS['BE_FFL']['groupscript'] 	= '\Agoat\CustomContentElementsBundle\Contao\GroupScript';


