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
 * Register back end module tables
 */
array_push($GLOBALS['BE_MOD']['design']['themes']['tables'], 'tl_content_blocks', 'tl_content_pattern');
$GLOBALS['BE_MOD']['design']['themes']['stylesheet'] = 'bundles/agoatcontentblocks/style.css';

 
//dump($GLOBALS['BE_MOD']['design']['themes']);

 /**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['getPageLayout'][] = array('Agoat\\ContentBlocks','loadAndRegisterBlockElements');
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('Agoat\\ContentBlocks','loadAndRegisterElementsWithGroups');

$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('Agoat\\ContentBlocks','setNewsArticleCallbacks');

$GLOBALS['TL_HOOKS']['parseTemplate'][] = array('Agoat\\ContentBlocks','addPageLayoutToBE');

$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][] = array('Agoat\\ContentBlocks','addContentBlockCSS');
$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][] = array('Agoat\\ContentBlocks','addContentBlockJS');
$GLOBALS['TL_HOOKS']['generatePage'][] = array('Agoat\\ContentBlocks','addLayoutJS');


/**
 * Content pattern
 */
$GLOBALS['TL_CTP'] = array
(
	'input' => array
	(
		'textfield'		=> 'PatternTextField',
		'textarea'		=> 'PatternTextArea',
		'selectfield'	=> 'PatternSelectField',
		'checkbox'		=> 'PatternCheckBox',
		'filetree'		=> 'PatternFileTree',
		'listwizard'	=> 'PatternListWizard',
		'tablewizard'	=> 'PatternTableWizard',
		'code'			=> 'PatternCode',
	),
	'layout' => array
	(
		'section'		=> 'PatternSection',
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


