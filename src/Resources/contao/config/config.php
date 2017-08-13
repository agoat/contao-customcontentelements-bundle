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
 * Register back end module (tables, css, overwritten classes)
 */
array_push($GLOBALS['BE_MOD']['design']['themes']['tables'], 'tl_elements', 'tl_pattern');

$GLOBALS['BE_MOD']['design']['themes']['stylesheet'][] = 'bundles/agoatcontentblocks/style.css';
$GLOBALS['BE_MOD']['content']['article']['stylesheet'][] = 'bundles/agoatcontentblocks/style.css';

$bundles = \System::getContainer()->getParameter('kernel.bundles');

if (isset($bundles['ContaoNewsBundle']))
{
	$GLOBALS['BE_MOD']['content']['news']['stylesheet'][] = 'bundles/agoatcontentblocks/style.css';
}


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['getPageLayout'][] = array('Agoat\\ContentElements\\Controller','registerBlockElements');
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('Agoat\\ContentElements\\Controller','registerBlockElements');

$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('Agoat\\ContentElements\\Controller','setNewsArticleCallbacks');

$GLOBALS['TL_HOOKS']['parseTemplate'][] = array('Agoat\\ContentElements\\Controller','addPageLayoutToBE');

$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][] = array('Agoat\\ContentElements\\Controller','addContentBlockCSS');
$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][] = array('Agoat\\ContentElements\\Controller','addContentBlockJS');
$GLOBALS['TL_HOOKS']['generatePage'][] = array('Agoat\\ContentElements\\Controller','addLayoutJS');


$GLOBALS['TL_HOOKS']['parseTemplate'][] = array('Agoat\\ContentElements\\Controller','hideContentValueVersions');

$GLOBALS['TL_HOOKS']['compareThemeFiles'][] = array('Agoat\\ContentElements\\Theme','compareContentBlockTables');
$GLOBALS['TL_HOOKS']['extractThemeFiles'][] = array('Agoat\\ContentElements\\Theme','importContentBlockTables');
$GLOBALS['TL_HOOKS']['exportTheme'][] = array('Agoat\\ContentElements\\Theme','exportContentBlockTables');

$GLOBALS['TL_HOOKS']['initializeSystem'][] = array('Agoat\\ContentElements\\Config','loadParameters');
 
 
/**
 * Content pattern
 */
$GLOBALS['TL_CTP'] = array
(
	'input' => array
	(
		'textfield'		=> 'Agoat\ContentElements\PatternTextField',
		'textarea'		=> 'Agoat\ContentElements\PatternTextArea',
		'selectfield'	=> 'Agoat\ContentElements\PatternSelectField',
		'checkbox'		=> 'Agoat\ContentElements\PatternCheckBox',
		'filetree'		=> 'Agoat\ContentElements\PatternFileTree',
		'pagetree'		=> 'Agoat\ContentElements\PatternPageTree',
		'article'		=> 'Agoat\ContentElements\PatternArticle',
		'listwizard'	=> 'Agoat\ContentElements\PatternListWizard',
		'tablewizard'	=> 'Agoat\ContentElements\PatternTableWizard',
		'code'			=> 'Agoat\ContentElements\PatternCode',
	),
	'layout' => array
	(
		'section'		=> 'Agoat\ContentElements\PatternSection',
		'explanation'	=> 'Agoat\ContentElements\PatternExplanation',
	),
	'element' => array
	(
		'visibility'	=> 'Agoat\ContentElements\PatternVisibility',
		'protection'	=> 'Agoat\ContentElements\PatternProtection',
	),
	'system' => array
	(
		'imagesize'		=> 'Agoat\ContentElements\PatternImageSize',
		'form'			=> 'Agoat\ContentElements\PatternForm',
		'module'		=> 'Agoat\ContentElements\PatternModule',
	),
);

/**
 * Content pattern not allowed in sub pattern
 */
$GLOBALS['TL_CTP_NA'] = array
(
	'subpattern' => array
	(
		'section',
		'visibility',
		'protection',
		'imagesize',
	),
	'multipattern' => array
	(
		'section',
		'visibility',
		'protection',
		'imagesize',
		'form',
		'module',
	),
);


/**
 * System pattern (with no values)
 */
$GLOBALS['TL_CTP_SYS'] = array('section', 'explanation', 'visibility', 'protection');



/**
 * Back end form fields (widgets)
 */
$GLOBALS['BE_FFL']['explanation'] 	= '\Agoat\ContentElements\Explanation';
$GLOBALS['BE_FFL']['visualselect'] 	= '\Agoat\ContentElements\VisualSelectMenu';
$GLOBALS['BE_FFL']['fileTree'] 		= '\Agoat\ContentElements\FileTree';
$GLOBALS['BE_FFL']['pageTree'] 		= '\Agoat\ContentElements\PageTree';

$GLOBALS['BE_FFL']['multigroup'] 		= 'MultiGroup';
$GLOBALS['BE_FFL']['multigroupstart'] 	= 'MultiGroupStart';
$GLOBALS['BE_FFL']['multigroupstop'] 	= 'MultiGroupStop';

