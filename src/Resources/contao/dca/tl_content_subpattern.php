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
 * Table tl_content_element
 */
$GLOBALS['TL_DCA']['tl_content_subpattern'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_content_pattern',
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'pid' => 'index',
			)
		)
	),
	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'pid' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'type' => array
		(
			'reference'               => &$GLOBALS['TL_LANG']['CTP'],
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		'alias' => array
		(
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'subPatternType' => array
		(
			'reference'               => &$GLOBALS['TL_LANG']['tl_content_pattern_subPatternType'],
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		'numberOfGroups' => array
		(
			'sql'                     => "smallint(5) unsigned NOT NULL default '0'"
		)
	)
);

/**
 * This table cannot be edited directly, redirect to tl_content_pattern
 */
if (\Input::get('table') == 'tl_content_subpattern')
{
	\Controller::redirect(preg_replace(array('/&(amp;)?table=[^& ]*/i','/&(amp;)?subpattern=[^& ]*/i'), array('&amp;table=tl_content_pattern',''), \Environment::get('requestUri')), 301);
}


