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
}


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['getPageLayout'][] = array('Agoat\\CustomContentElementsBundle\\Contao\\Controller','registerBlockElements');
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('Agoat\\CustomContentElementsBundle\\Contao\\Controller','registerBlockElements');

$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('Agoat\\CustomContentElementsBundle\\Contao\\Config','setNewsArticleCallbacks');

$GLOBALS['TL_HOOKS']['parseTemplate'][] = array('Agoat\\CustomContentElementsBundle\\Contao\\Controller','addPageLayoutToBE');

$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][] = array('Agoat\\CustomContentElementsBundle\\Contao\\Controller','addContentElementsCSS');
$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][] = array('Agoat\\CustomContentElementsBundle\\Contao\\Controller','addContentElementsJS');
$GLOBALS['TL_HOOKS']['generatePage'][] = array('Agoat\\CustomContentElementsBundle\\Contao\\Controller','addLayoutJS');

$GLOBALS['TL_HOOKS']['parseTemplate'][] = array('Agoat\\CustomContentElementsBundle\\Contao\\Versions','hideDataTableVersions');


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
		),
		'textarea'		=> array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternTextArea',
			'data'			=> true,
			'output'		=> true,
		),
		'selectfield'	=> array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternSelectField',
			'data'			=> true,
			'output'		=> true,
		),
		'checkbox'		=> array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternCheckBox',
			'data'			=> true,
			'output'		=> true,
		),
		'filetree'		=> array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternFileTree',
			'data'			=> true,
			'output'		=> true,
		),
		'pagetree'		=> array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternPageTree',
			'data'			=> true,
			'output'		=> true,
		),
		'listwizard'	=> array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternListWizard',
			'data'			=> true,
			'output'		=> true,
		),
		'tablewizard'	=> array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternTableWizard',
			'data'			=> true,
			'output'		=> true,
		),
		'code'			=> array
		(
			'class'			=> 'Agoat\CustomContentElementsBundle\Contao\PatternCode',
			'data'			=> true,
			'output'		=> true,
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
		)
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
			'output'			=> true,
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


