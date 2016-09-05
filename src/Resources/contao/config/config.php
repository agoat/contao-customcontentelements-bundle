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

array_push($GLOBALS['BE_MOD']['design']['themes']['tables'], 'tl_content_blocks', 'tl_content_pattern');
$GLOBALS['BE_MOD']['design']['themes']['stylesheet'] = 'bundles/agoatcontentblocks/style.css';

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
		'selectfield'	=> 'PatternSelectField',
		'checkbox'		=> 'PatternCheckBox',
		'filetree'		=> 'PatternFileTree',
		'listwizard'	=> 'PatternListWizard',
		'tablewizard'	=> 'PatternTableWizard',
		'code'			=> 'PatternCode',
	),
	'layout' => array
	(
		'section'		=> 'Agoat\ContentBlocks\PatternSection',
		'explanation'	=> 'PatternExplanation',
	),
	'element' => array
	(
		'visibility'	=> 'PatternVisibility',
		'protection'	=> 'PatternProtection',
	),
	'system' => array
	(
		'form'			=> 'PatternForm',
		'module'		=> 'PatternModule',
	),
);



/**
 * system pattern (with no values)
 */
$GLOBALS['TL_SYS_PATTERN'] = array('explanation', 'visibility', 'protection');



/**
 * Back end form fields (widgets)
 */
$GLOBALS['BE_FFL']['explanation'] = 'Explanation';
$GLOBALS['BE_FFL']['visualselect'] = 'VisualSelectMenu';


