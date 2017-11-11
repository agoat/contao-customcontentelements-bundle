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


/**
 * Style sheet
 */
if (TL_MODE == 'BE')
{
	$GLOBALS['TL_CSS'][] = 'bundles/agoatcontentelements/style.css|static';
	$GLOBALS['TL_JAVASCRIPT'][] = 'bundles/agoatcontentelements/core.js';
}


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['getPageLayout'][] = array('Agoat\\ContentElements\\Controller','registerBlockElements');
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('Agoat\\ContentElements\\Controller','registerBlockElements');
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('Agoat\\ContentElements\\Controller','handleSubPatternTable');

$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('Agoat\\ContentElements\\Controller','setNewsArticleCallbacks');

$GLOBALS['TL_HOOKS']['parseTemplate'][] = array('Agoat\\ContentElements\\Controller','addPageLayoutToBE');

$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][] = array('Agoat\\ContentElements\\Controller','addContentBlockCSS');
$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][] = array('Agoat\\ContentElements\\Controller','addContentBlockJS');
$GLOBALS['TL_HOOKS']['generatePage'][] = array('Agoat\\ContentElements\\Controller','addLayoutJS');

$GLOBALS['TL_HOOKS']['parseTemplate'][] = array('Agoat\\ContentElements\\Versions','hideDataTableVersions');

$GLOBALS['TL_HOOKS']['executePostActions'][] = array('Agoat\\ContentElements\\Ajax','executeGroupActions');


$GLOBALS['TL_HOOKS']['compareThemeFiles'][] = array('Agoat\\ContentElements\\Theme','compareTables');
$GLOBALS['TL_HOOKS']['extractThemeFiles'][] = array('Agoat\\ContentElements\\Theme','importTables');
$GLOBALS['TL_HOOKS']['exportTheme'][] = array('Agoat\\ContentElements\\Theme','exportTables');

$GLOBALS['TL_HOOKS']['initializeSystem'][] = array('Agoat\\ContentElements\\Config','loadParameters');
 

/**
 * Pattern elements
 */
$GLOBALS['TL_CTP'] = array
(
	'input' => array
	(
		'textfield'		=> array
		(
			'class'			=> 'Agoat\ContentElements\PatternTextField',
			'data'			=> true,
			'output'		=> true,
			'childOf'		=> array('subpattern', 'multipattern'),
		),
		'textarea'		=> array
		(
			'class'			=> 'Agoat\ContentElements\PatternTextArea',
			'data'			=> true,
			'output'		=> true,
			'childOf'		=> array('subpattern', 'multipattern'),
		),
		'selectfield'	=> array
		(
			'class'			=> 'Agoat\ContentElements\PatternSelectField',
			'data'			=> true,
			'output'		=> true,
			'childOf'		=> array('subpattern', 'multipattern'),
		),
		'checkbox'		=> array
		(
			'class'			=> 'Agoat\ContentElements\PatternCheckBox',
			'data'			=> true,
			'output'		=> true,
			'childOf'		=> array('subpattern', 'multipattern'),
		),
		'filetree'		=> array
		(
			'class'			=> 'Agoat\ContentElements\PatternFileTree',
			'data'			=> true,
			'output'		=> true,
			'childOf'		=> array('subpattern', 'multipattern'),
		),
		'pagetree'		=> array
		(
			'class'			=> 'Agoat\ContentElements\PatternPageTree',
			'data'			=> true,
			'output'		=> true,
			'childOf'		=> array('subpattern', 'multipattern'),
		),
		'article'		=> array
		(
			'class'			=> 'Agoat\ContentElements\PatternArticle',
			'data'			=> true,
			'output'		=> true,
			'childOf'		=> array('subpattern', 'multipattern'),
		),
		'listwizard'	=> array
		(
			'class'			=> 'Agoat\ContentElements\PatternListWizard',
			'data'			=> true,
			'output'		=> true,
			'childOf'		=> array('subpattern', 'multipattern'),
		),
		'tablewizard'	=> array
		(
			'class'			=> 'Agoat\ContentElements\PatternTableWizard',
			'data'			=> true,
			'output'		=> true,
			'childOf'		=> array('subpattern', 'multipattern'),
		),
		'code'			=> array
		(
			'class'			=> 'Agoat\ContentElements\PatternCode',
			'data'			=> true,
			'output'		=> true,
			'childOf'		=> array('subpattern', 'multipattern'),
		)
	),
	'layout' => array
	(
		'section' => array
		(
			'class'			=> 'Agoat\ContentElements\PatternSection',
		),
		'explanation' => array
		(
			'class'			=> 'Agoat\ContentElements\PatternExplanation',
			'childOf'		=> array('subpattern', 'multipattern'),
		),
		'subpattern' => array
		(
			'class'			=> 'Agoat\ContentElements\PatternSubPattern',
			'data'			=> true,
			'output'		=> true,
			'subpattern'	=> true,
			'childOf'		=> array('subpattern', 'multipattern'),
		),
		'multipattern' => array
		(
			'class'			=> 'Agoat\ContentElements\PatternMultiPattern',
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
			'class'			=> 'Agoat\ContentElements\PatternVisibility',
			'unique'		=> true,
			'output'		=> true,
		),
		'protection' => array
		(
			'class'			=> 'Agoat\ContentElements\PatternProtection',
			'unique'		=> true,
			'output'		=> true,
		),
	),
	'system' => array
	(
		'imagesize' => array
		(
			'class'			=> 'Agoat\ContentElements\PatternImageSize',
			'output'		=> true,
			'childOf'		=> array('subpattern'),
		),
		'form' => array
		(
			'class'			=> 'Agoat\ContentElements\PatternForm',
			'unique'		=> true,
			'output'		=> true,
		),
		'module' => array
		(
			'class'			=> 'Agoat\ContentElements\PatternModule',
			'unique'		=> true,
			'output'			=> true,
		)
	)
);


/**
 * Back end form fields (widgets)
 */
$GLOBALS['BE_FFL']['explanation'] 	= '\Agoat\ContentElements\Explanation';
$GLOBALS['BE_FFL']['visualselect'] 	= '\Agoat\ContentElements\VisualSelectMenu';
$GLOBALS['BE_FFL']['fileTree'] 		= '\Agoat\ContentElements\FileTree';
$GLOBALS['BE_FFL']['pageTree'] 		= '\Agoat\ContentElements\PageTree';

$GLOBALS['BE_FFL']['group'] 		= '\Agoat\ContentElements\Group';
$GLOBALS['BE_FFL']['groupstart'] 	= '\Agoat\ContentElements\GroupStart';
$GLOBALS['BE_FFL']['groupstop'] 	= '\Agoat\ContentElements\GroupStop';
$GLOBALS['BE_FFL']['groupscript'] 	= '\Agoat\ContentElements\GroupScript';

