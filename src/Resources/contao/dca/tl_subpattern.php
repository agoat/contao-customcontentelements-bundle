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
 * Table tl_subpattern
 */
$GLOBALS['TL_DCA']['tl_subpattern'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_pattern',
		'notEditable'                 => true,
		'notCopyable'                 => true,
		'doNotCopyRecords'            => true,
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
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'title' => array
		(
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'alias' => array
		(
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'subPatternType' => array
		(
			'reference'               => &$GLOBALS['TL_LANG']['tl_pattern_subPatternType'],
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'numberOfGroups' => array
		(
			'sql'                     => "smallint(5) unsigned NOT NULL default '0'"
		)
	)
);


/**
 * This table cannot be edited directly, redirect to tl_pattern
 */
if (\Input::get('table') == 'tl_subpattern')
{
	\Controller::redirect(preg_replace(array('/&(amp;)?table=[^& ]*/i','/&(amp;)?subpattern=[^& ]*/i'), array('&amp;table=tl_content_pattern',''), \Environment::get('requestUri')), 301);
}

