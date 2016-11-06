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

array_push($GLOBALS['BE_MOD']['design']['themes']['tables'], 'tl_content_blocks', 'tl_content_pattern', 'tl_content_subpattern', 'tl_content_multipattern');
$GLOBALS['BE_MOD']['design']['themes']['stylesheet'] = 'bundles/agoatcontentblocks/style.css';
$GLOBALS['BE_MOD']['content']['article']['stylesheet'] = 'bundles/agoatcontentblocks/style.css';

$GLOBALS['BE_MOD']['design']['themes']['importTheme'] = array('Agoat\\ContentBlocks\\Theme', 'importTheme');
$GLOBALS['BE_MOD']['design']['themes']['exportTheme'] = array('Agoat\\ContentBlocks\\Theme', 'exportTheme');



 /**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['getPageLayout'][] = array('Agoat\\ContentBlocks\\Controller','loadAndRegisterBlockElements');
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('Agoat\\ContentBlocks\\Controller','loadAndRegisterElementsWithGroups');

$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('Agoat\\ContentBlocks\\Controller','setNewsArticleCallbacks');

$GLOBALS['TL_HOOKS']['parseTemplate'][] = array('Agoat\\ContentBlocks\\Controller','addPageLayoutToBE');

$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][] = array('Agoat\\ContentBlocks\\Controller','addContentBlockCSS');
$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][] = array('Agoat\\ContentBlocks\\Controller','addContentBlockJS');
$GLOBALS['TL_HOOKS']['generatePage'][] = array('Agoat\\ContentBlocks\\Controller','addLayoutJS');


 
 
/**
 * Content pattern
 */
$GLOBALS['TL_CTP'] = array
(
	'input' => array
	(
		'textfield'		=> 'Agoat\ContentBlocks\PatternTextField',
		'textarea'		=> 'Agoat\ContentBlocks\PatternTextArea',
		'selectfield'	=> 'Agoat\ContentBlocks\PatternSelectField',
		'checkbox'		=> 'Agoat\ContentBlocks\PatternCheckBox',
		'filetree'		=> 'Agoat\ContentBlocks\PatternFileTree',
		'listwizard'	=> 'Agoat\ContentBlocks\PatternListWizard',
		'tablewizard'	=> 'Agoat\ContentBlocks\PatternTableWizard',
		'code'			=> 'Agoat\ContentBlocks\PatternCode',
	),
	'layout' => array
	(
		'section'		=> 'Agoat\ContentBlocks\PatternSection',
		'explanation'	=> 'Agoat\ContentBlocks\PatternExplanation',
		'subpattern'	=> 'Agoat\ContentBlocks\PatternSubPattern',
		'multipattern'	=> 'Agoat\ContentBlocks\PatternMultiPattern',
	),
	'element' => array
	(
		'visibility'	=> 'Agoat\ContentBlocks\PatternVisibility',
		'protection'	=> 'Agoat\ContentBlocks\PatternProtection',
	),
	'system' => array
	(
		'form'			=> 'Agoat\ContentBlocks\PatternForm',
		'module'		=> 'Agoat\ContentBlocks\PatternModule',
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
	),
	'multipattern' => array
	(
		'section',
		'visibility',
		'protection',
		'form',
		'module',
	),
);

/**
 * Sub pattern
 */
$GLOBALS['TL_CTP_SUB'] = array('subpattern', 'multipattern');


/**
 * System pattern (with no values)
 */
$GLOBALS['TL_CTP_SYS'] = array('section', 'explanation', 'visibility', 'protection');
$GLOBALS['TL_SYS_PATTERN'] = array('section', 'explanation', 'visibility', 'protection');



/**
 * Back end form fields (widgets)
 */
$GLOBALS['BE_FFL']['explanation'] = 'Explanation';
$GLOBALS['BE_FFL']['visualselect'] = 'VisualSelectMenu';

$GLOBALS['BE_FFL']['multigroup'] = 'MultiGroup';
$GLOBALS['BE_FFL']['multigroupstart'] = 'MultiGroupStart';
$GLOBALS['BE_FFL']['multigroupstop'] = 'MultiGroupStop';

