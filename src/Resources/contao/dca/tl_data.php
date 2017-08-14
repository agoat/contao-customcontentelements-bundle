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
$GLOBALS['TL_DCA']['tl_data'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'enableVersioning'            => true,
		'ptable'                      => 'tl_content',

		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'pid' => 'index',
				'pid,pattern' => 'index',
				'pid,pattern,parent' => 'index'
			)
		)
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'	=> "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array	// tl_content.id
		(
			'sql'	=> "int(10) unsigned NOT NULL default '0'"
		),
		'pattern' => array	// tl_pattern.alias
		(
			'sql'	=> "varchar(64) NOT NULL default ''"
		),
		'parent' => array	// tl_data.id of parent pattern (subpattern data)
		(
			'sql'	=> "int(10) unsigned NOT NULL default '0'"
		),
		'tstamp' => array
		(
			'sql'	=> "int(10) unsigned NOT NULL default '0'"
		),

		// value columns 
		'text' => array
		(
			'sql'	=> "mediumtext NULL"
		),
		'singleTextField' => array
		(
			'sql'	=> "varchar(255) NOT NULL default ''"
		),
		'multiTextField' => array
		(
			'sql'	=> "varchar(1022) NOT NULL default ''"
		),
		'inputUnit' => array
		(
			'sql'	=> "varchar(255) NOT NULL default ''"
		),
		'singleSelectField' => array
		(
			'sql'	=> "varchar(128) NOT NULL default ''"
		),
		'multiSelectField' => array
		(
			'sql'	=> "varchar(1022) NOT NULL default ''"
		),
		'checkBox' => array
		(
			'sql'	=> "char(1) NOT NULL default ''"
		),
		'listItems' => array
		(
			'sql'	=> "blob NULL"
		),
		'tableItems' => array
		(
			'sql'	=> "mediumblob NULL"
		),
		'singlePage' => array
		(
			'sql'	=> "int(10) unsigned NOT NULL default '0'"
		),
		'multiPage' => array
		(
			'sql'	=> "blob NULL"
		),
		'orderPage' => array
		(
			'sql'	=> "blob NULL"
		),
		'singleSRC' => array
		(
			'sql'	=> "binary(16) NULL"
		),
		'multiSRC' => array
		(
			'sql'	=> "blob NULL"
		),
		'orderSRC' => array
		(
			'sql'	=> "blob NULL"
		),
		'sortBy' => array
		(
			'sql'	=> "varchar(32) NOT NULL default ''"
		),		
		'size' => array
		(
			'sql'	=> "varchar(64) NOT NULL default ''"
		),
		'highlight' => array
		(
			'sql'	=> "varchar(64) NOT NULL default ''"
		),
		'count' => array
		(
			'sql'	=> "int(10) unsigned NOT NULL default '0'"
		),
	)
);



