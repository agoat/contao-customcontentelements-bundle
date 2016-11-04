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
 * Table tl_content_pattern
 */
$GLOBALS['TL_DCA']['tl_content_pattern'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'switchToEdit'                => false,
		'enableVersioning'            => true,
		'ptable'                      => 'tl_content_blocks',
		'ctable'                      => array('tl_content_subpattern'),
		'dynamicPtable'				  => true,
		'onload_callback' => array
		(
			//array('tl_content_pattern', 'checkPermission'),
			array('tl_content_pattern', 'showAlreadyUsedHint')
		),
		'onsubmit_callback'			  => array
		(
			array('tl_content_pattern', 'correctGroups'),
			array('tl_content_pattern', 'saveSubPattern'),
		),
		'ondelete_callback'			  => array
		(
			array('tl_content_pattern', 'deleteSubPattern'),
		),
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'pid,ptable,invisible,sorting' => 'index',
				'pid,ptable,suboption,invisible,sorting' => 'index'
			)
		)
	),
	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 4,
			'fields'                  => array('sorting'),
			'headerFields'            => array('title','description','template'),
			'panelLayout'             => 'filter;search,limit',
			'child_record_callback'   => array('tl_content_pattern', 'previewElementPattern')
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_content_pattern']['edit'],
				'icon'                => 'edit.svg',
				'button_callback'     => array('tl_content_pattern', 'patternButton')
			),
			'editheader' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_content_pattern']['editheader'],
				'href'                => 'act=edit',
				'icon'                => 'header.svg',
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_content_pattern']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.svg',
				'attributes'          => 'onclick="Backend.getScrollOffset()"'
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_content_pattern']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.svg',
				'attributes'          => 'onclick="Backend.getScrollOffset()"'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_content_pattern']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.svg',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_content_pattern']['toggle'],
				'icon'                => 'visible.svg',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_content_pattern', 'toggleIcon')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_content_pattern']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.svg'
			)
		)
	),
	// Palettes
	'palettes' => array
	(
		'__selector__'                => array('type','source','multiSource','picker','subPatternType'),
		'default'                     => '{type_legend},type',
		// input
		'textfield'					  => '{type_legend},type;{textfield_legend},minLength,maxLength,rgxp,defaultValue,multiple,picker;{label_legend},label,description;{pattern_legend},alias,mandatory,classClr,classLong;{invisible_legend},invisible',
		'textarea'					  => '{type_legend},type;{textarea_legend},rteTemplate;{label_legend},label,description;{pattern_legend},alias,mandatory,;{invisible_legend},invisible',
		'code'					 	  => '{type_legend},type;{code_legend},highlight;{label_legend},label,description;{pattern_legend},alias,mandatory,;{invisible_legend},invisible',
		'selectfield'				  => '{type_legend},type;{select_legend},options,blankOption;{label_legend},label,description;{pattern_legend},alias,mandatory,classClr;{invisible_legend},invisible',
		'checkbox'					  => '{type_legend},type;{label_legend},label,description;{pattern_legend},alias,mandatory,classClr;{invisible_legend},invisible',
		'listwizard'				  => '{type_legend},type;{label_legend},label,description;{pattern_legend},alias,mandatory;{invisible_legend},invisible',
		'tablewizard'				  => '{type_legend},type;{label_legend},label,description;{pattern_legend},alias,mandatory;{invisible_legend},invisible',
		'filetree'					  => '{type_legend},type;{source_legend},source;{multiSource_legend},multiSource;{label_legend},label,description;{pattern_legend},alias,mandatory,classClr;{invisible_legend},invisible',
		// layout
		'section'					  => '{type_legend},type;{section_legend},label,hidden;{invisible_legend},invisible',
		'explanation'				  => '{type_legend},type;{explanation_legend},explanation;{invisible_legend},invisible',
		'subpattern'				  => '{type_legend},type;{subpattern_legend},subPatternType;{label_legend},label,description;{pattern_legend},alias;{invisible_legend},invisible',
		'multipattern'				  => '{type_legend},type;{multipattern_legend},multiPatternMax;{label_legend},label,description;{pattern_legend},alias;{invisible_legend},invisible',
		// element
		'visibility'				  => '{type_legend},type;{visibility_legend},canChangeStart,canChangeStop;{invisible_legend},invisible',
		'protection'				  => '{type_legend},type;{protection_legend},groups,canChangeGroups;{invisible_legend},invisible',
		// system
		'form'						  => '{type_legend},type;{form_legend},form;{pattern_legend},alias;{invisible_legend},invisible',
		'module'					  => '{type_legend},type;{module_legend},module;{pattern_legend},alias;{invisible_legend},invisible',
		
	),
	// Subpalettes
	'subpalettes' => array
	(
		'source_image'				  => 'size,canChangeSize,sizeList,canEnterSize',
		'source_custom'				  => 'customExtension',
		'multiSource'				  => 'sortBy,canChangeSortBy,numberOfItems,metaIgnore',
		'picker_unit'				  => 'units',
		'replicable'				  => 'maxReplicas',
		'subPatternType_options'	  => 'options',
	),
	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'ptable' => array
		(
			'sql'                     => "varchar(64) NOT NULL default 'tl_content_blocks'"
		),
		'sorting' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'suboption' => array
		(
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'type' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['type'],
			'default'                 => 'textfield',
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'select',
			'options_callback'        => array('tl_content_pattern', 'getPatternForContentBlock'),
			'reference'               => &$GLOBALS['TL_LANG']['CTP'],
			'eval'                    => array('helpwizard'=>true, 'chosen'=>true, 'submitOnChange'=>true),
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		'alias' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['alias'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50'),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'invisible' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['invisible'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'label' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['label'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
			'sql'                     => "varchar(128) NOT NULL default ''"
		),
		'description' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['description'],
			'exclude'                 => true,
			'inputType'               => 'textarea',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'long clr'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'mandatory' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['mandatory'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'classLong' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['classLong'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'classClr' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['classClr'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'subPatternType' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['subType'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'                 => array('button' => 'CheckBox', 'options' => 'Select field'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_content_pattern'],
			'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'w50'),
			'sql'                     => "varchar(16) NOT NULL default ''"
		),
		'multiPatternMax' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['multiPatternMax'],
			'exclude'                 => true,
			'default'				  => 100,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'natural', 'maxlength'=>3, 'minval'=>1, 'maxval'=>100, 'tl_class'=>'w50 clr'),
			'sql'                     => "smallint(4) unsigned NOT NULL default '0'"
		),
		'hidden' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['hidden'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'explanation' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['explanation'],
			'exclude'                 => true,
			'inputType'               => 'textarea',
			'eval'                    => array('mandatory'=>true, 'rte'=>'tinyMCE_explanation'),
			'sql'                     => "mediumtext NULL"
		),
		'style' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['style'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50 clr'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'canChangeStart' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['canChangeStart'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12 clr'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'canChangeStop' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['canChangeStop'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'groups' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['groups'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'foreignKey'              => 'tl_member_group.name',
			'eval'                    => array('mandatory'=>true, 'multiple'=>true),
			'sql'                     => "blob NULL",
			'relation'                => array('type'=>'hasMany', 'load'=>'lazy')
		),
		'canChangeGroups' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['canChangeGroups'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),

		'rteTemplate' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['rteTemplate'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'default'				  => 'be_tinyMCE_standard',
			'flag'                    => 11,
			'options_callback'        => array('tl_content_pattern', 'getRteTemplates'),
			'eval'                    => array('tl_class'=>'w50'),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),

		'highlight' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['highlight'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'                 => array('HTML', 'HTML5', 'XML', 'JavaScript', 'CSS', 'SCSS', 'PHP', 'JSON'),
			'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
			'sql'                     => "varchar(32) NOT NULL default ''"
		),


		'source' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['source'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'                 => array('all', 'image', 'video', 'audio', 'custom'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_content_pattern_source'],
			'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'w50 clr'),
			'save_callback' => array
			(
				array ('tl_content_pattern','setSourceOptions')
			),
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		'customExtension' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['customExtension'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),

		'size' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['size'],
			'exclude'                 => true,
			'inputType'               => 'imageSize',
			'reference'               => &$GLOBALS['TL_LANG']['MSC'],
			'eval'                    => array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50 clr'),
			'options_callback' => function ()
			{
				return \System::getContainer()->get('contao.image.image_sizes')->getAllOptions();
			},
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'canChangeSize' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['canChangeSize'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'sizeList' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['sizeList'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'eval'                    => array('multiple'=>true, 'includeBlankOption'=>true, 'size'=>10, 'tl_class'=>'w50 clr', 'chosen'=>true),
			'options_callback' => function ()
			{
				return System::getContainer()->get('contao.image.image_sizes')->getAllOptions()['image_sizes'];
			},
			'load_callback' => array
			(
				array ('tl_content_pattern','defaultSizes')
			),
			'save_callback' => array
			(
				array ('tl_content_pattern','defaultSizes')
			),
			'sql'                     => "blob NULL"		
		),
		'canEnterSize' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['canEnterSize'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),

		'multiSource' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['multiSource'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'w50 m12 clr'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'sortBy' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['sortBy'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'                 => array('custom', 'name_asc', 'name_desc', 'date_asc', 'date_desc', 'random', 'html5media'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_content_pattern_sortby'],
			'eval'                    => array('tl_class'=>'w50 clr'),
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		'canChangeSortBy' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['canChangeSortBy'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'metaIgnore' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['metaIgnore'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'numberOfItems' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['numberOfItems'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'natural', 'tl_class'=>'w50 clr'),
			'sql'                     => "smallint(5) unsigned NOT NULL default '0'"
		),

		'minLength' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['minLength'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'default'                 => '0',
			'eval'                    => array('rgxp'=>'natural', 'maxlength'=>3, 'maxval'=>255, 'tl_class'=>'w50'),
			'sql'                     => "smallint(5) unsigned NOT NULL default '255'"
		),
		'maxLength' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['maxLength'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'default'                 => '255',
			'eval'                    => array('rgxp'=>'natural', 'maxlength'=>3, 'maxval'=>255, 'tl_class'=>'w50'),
			'sql'                     => "smallint(5) unsigned NOT NULL default '255'"
		),
		'rgxp' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['rgxp'],
			'inputType'               => 'select',
			'options'                 => array('natural', 'prcnt', 'digit', 'alpha', 'alnum', 'extnd', 'date', 'time', 'datim', 'phone', 'email', 'url'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_content_pattern'],
			'eval'                    => array('helpwizard'=>true, 'includeBlankOption'=>true, 'tl_class'=>'w50'),
			'sql'                     => "varchar(16) NOT NULL default ''"
		),
		'defaultValue' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['defaultValue'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'multiple' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['multiple'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'				  => array(2, 3, 4),
			'eval'                    => array('submitOnChange'=>true, 'includeBlankOption'=>true, 'tl_class'=>'w50'),
			'save_callback' => array
			(
				array ('tl_content_pattern','setMultipleOptions')
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'picker' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['picker'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'				  => array('datetime', 'color', 'page', 'unit'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_content_pattern'],
			'eval'                    => array('submitOnChange'=>true, 'includeBlankOption'=>true, 'tl_class'=>'w50'),
			'save_callback' => array
			(
				array ('tl_content_pattern','setPickerOptions')
			),
			'sql'                     => "varchar(16) NOT NULL default ''"
		),
		'units' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['units'],
			'exclude'                 => true,
			'inputType'               => 'optionWizard',
			'eval'                    => array('allowHtml'=>true),
			'sql'                     => "blob NULL"
		),
		'options' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['options'],
			'exclude'                 => true,
			'inputType'               => 'optionWizard',
			'eval'                    => array('allowHtml'=>true, 'tl_class'=>'clr'),
			'sql'                     => "blob NULL"
		),
		'blankOption' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['blankOption'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'multiSelect' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['multiSelect'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'form' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['form'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options_callback'        => array('tl_content_pattern', 'getForms'),
			'eval'                    => array('mandatory'=>true, 'chosen'=>true, 'submitOnChange'=>true, 'tl_class'=>'w50'),
			'wizard' => array
			(
				array('tl_content_pattern', 'editForm')
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'module' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_content_pattern']['module'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options_callback'        => array('tl_content_pattern', 'getModules'),
			'eval'                    => array('mandatory'=>true, 'chosen'=>true, 'submitOnChange'=>true),
			'wizard' => array
			(
				array('tl_content_pattern', 'editModule')
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),

	)
);


/**
 * Dynamically change parent table when editing subpattern
 */

if (\Input::get('spid') !== null || \Input::get('pid') !== null)
{

	// use the parent of the parent to check for a sub pattern
	if (\Input::get('spid') === null)
	{
		$objParent = \ContentPatternModel::findById(\ContentPatternModel::findById(\Input::get('pid'))->pid);
	}
	else
	{
		$objParent = \ContentPatternModel::findById(\Input::get('spid'));
		
	}
	
	if ($objParent !== null)
	{

		$GLOBALS['TL_DCA']['tl_content_pattern']['config']['ptable'] = $objParent->ptable;
		$GLOBALS['TL_DCA']['tl_content_pattern']['list']['sorting']['headerFields'] =  array('type','alias');
		
		// add extra info for the sub pattern types
		if ($objParent->type == 'subpattern')
		{
			$GLOBALS['TL_DCA']['tl_content_pattern']['list']['sorting']['headerFields'][] =  'subPatternType';
			$GLOBALS['TL_DCA']['tl_content_pattern']['fields']['type']['options_callback'] =array('tl_content_pattern', 'getPatternForSubPattern');
			
		}
		else if ($objParent->type == 'multipattern')
		{
			$GLOBALS['TL_DCA']['tl_content_pattern']['list']['sorting']['headerFields'][] =  'multiPatternMax';
			$GLOBALS['TL_DCA']['tl_content_pattern']['fields']['type']['options_callback'] = array('tl_content_pattern', 'getPatternForMultiPattern');
		}
		
		
		// in edit mode set the ptable in case ..
		if (\Input::get('act') !== null)
		{
			// if spid reference to a sub pattern set ptable always to tl_content_subpattern
			if (in_array($objParent->type, $GLOBALS['TL_CTP_SUB']) && \Input::get('id') != \Input::get('spid'))
			{
					$GLOBALS['TL_DCA']['tl_content_pattern']['config']['ptable'] = 'tl_content_subpattern';
			}
		
		}
		// in table view set ptable always
		else if (\Input::get('id') == \Input::get('spid'))
		{
			$GLOBALS['TL_DCA']['tl_content_pattern']['config']['ptable'] = 'tl_content_subpattern';
		}
	

	
		// set the filter for the subpattern option
		if ($objParent->type == 'subpattern' && $objParent->subPatternType == 'options')
		{
		// set callbacks and filter
			$GLOBALS['TL_DCA']['tl_content_pattern']['config']['onload_callback'][] = array('tl_content_pattern', 'subPatternFilter');
			$GLOBALS['TL_DCA']['tl_content_pattern']['list']['sorting']['panel_callback']['subPatternFilter'] = array('tl_content_pattern', 'generatesubPatternFilter');

			$GLOBALS['TL_DCA']['tl_content_pattern']['list']['sorting']['panelLayout'] = str_replace('filter', 'subPatternFilter;filter', $GLOBALS['TL_DCA']['tl_content_pattern']['list']['sorting']['panelLayout']);
		}
	}
}
//dump($GLOBALS['TL_DCA']['tl_content_pattern']['config']['ptable']);





/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Arne Stappen (aGoat) <https://github.com/agoat>
 */
class tl_content_pattern extends Backend
{


	protected $table = 'tl_content_pattern';
	
	
	
	
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	
	// panel_callback
	public function generatesubPatternFilter($dc) 
	{ 
		$session = $this->Session->getData();
		$filter = 'tl_content_pattern'.CURRENT_ID;
		
		$return = '<div class="tl_filter tl_subpanel"><strong>' . $GLOBALS['TL_LANG']['tl_content_pattern']['suboption'] . '</strong>
<select name="suboption" id="suboption" class="tl_select" onchange="this.form.submit()">';
		
		$objPattern = \ContentPatternModel::findByPk(\Input::get('spid'));
		
		if ($objPattern !== null)
		{
			foreach (StringUtil::deserialize($objPattern->options) as $arrOption)
			{
				$return .='<option value="' . $arrOption['value'] . '"' . (($session['filter'][$filter]['suboption'] == $arrOption['value'])? ' selected="selected"' : '') . '>' . $arrOption['label'] . '</option>';
			}
		}

		$return .='</select></div>';


		return $return;
	}

	
	// onload_callback
	public function subPatternFilter($dc) 
	{ 
		$session = $this->Session->getData();
		$filter = 'tl_content_pattern'.CURRENT_ID;
		
		if (\Input::post('FORM_SUBMIT') == 'tl_filters')
		{
			// Validate the user input
			if (\Input::post('suboption'))
			{
				$session['filter'][$filter]['suboption'] = \Input::Post('suboption');
			}
			else
			{
				unset($session['filter'][$filter]['suboption']);
			}
			
			$this->Session->setData($session);
		}

		$GLOBALS['TL_DCA']['tl_content_pattern']['list']['sorting']['filter']['suboption'] = array('suboption=?', $session['filter'][$filter]['suboption']);
	}

	/**
	 * Return the pattern edit button
	 *
	 * @param array  $row
	 * @param string $href
	 * @param string $label
	 * @param string $title
	 * @param string $icon
	 * @param string $attributes
	 *
	 * @return string
	 */
	public function patternButton($row, $href, $label, $title, $icon, $attributes)
	{
		if (in_array($row['type'], $GLOBALS['TL_CTP_SUB']))
		{
			return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id'].'&amp;spid='.$row['id'],true, array('act','mode')).'" title="'.\StringUtil::specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ';
		}
		
	}	
	
	/**
	 * Add the type of content pattern
	 *
	 * @param array $arrRow
	 *
	 * @return string
	 */
	public function previewElementPattern($arrRow)
	{
		$key = $arrRow['invisible'] ? 'unpublished' : 'published';
		$type = $GLOBALS['TL_LANG']['CTP'][$arrRow['type']][0] ?: '&nbsp;';

		// prepare option string (alias / replicable)
		switch ($arrRow['type'])
		{
			case 'section':
			case 'explanation':
			case 'protection':
			case 'visibility':
				break;
			
			default:
				$options = '<span style="color: #b3b3b3 ;padding-left: 3px"> (Template alias: $this->' . $arrRow['alias'] . ')</span>';
				break;
		}
		
		// get pattern model and call view method
		$objPattern = new ContentPatternModel();
		$objPattern->setRow($arrRow);
		
		$strClass = \Agoat\ContentBlocks\Pattern::findClass($objPattern->type);
				
		if (!class_exists($strClass))
		{
			static::log('Pattern element class "'.$strClass.'" (pattern element "'.$objPattern->type.'") does not exist', __METHOD__, TL_ERROR);
		}
		else
		{
			$objPatternClass = new $strClass($objPattern);	
			$strPatternView = $objPatternClass->view();
		}

		
		return '<div class="cte_type ' . $key . '">' . $type . $options . '</div><div style="padding: 5px 0 10px;">' . $strPatternView . '</div>';
	}


	
	/**
	 * Add the type of content pattern
	 *
	 * @param array $arrRow
	 *
	 * @return string
	 */
	public function setPickerOptions($value, $dc)
	{
		$db = Database::getInstance();
		
		switch ($value)
		{
			case 'datetime':
				if (!in_array($dc->activeRecord->rgxp, array('date', 'time', 'datim')))
				{
					// change rgxp in database
					$db->prepare("UPDATE " . $this->table . " SET rgxp=? WHERE id=?")
					   ->execute('date', $dc->activeRecord->id);
				}
				if ($dc->activeRecord->multiple)
				{
					// reset multiple in database
					$db->prepare("UPDATE " . $this->table . " SET multiple=? WHERE id=?")
					   ->execute('', $dc->activeRecord->id);
				}
				break;
	
			case 'color':
				if (!in_array($dc->activeRecord->rgxp, array('alnum', 'extnd')))
				{
					// change rgxp in database
					$db->prepare("UPDATE " . $this->table . " SET rgxp=? WHERE id=?")
					   ->execute('', $dc->activeRecord->id);
				}
				break;
				
			case 'page':
				if ($dc->activeRecord->rgxp != 'url')
				{
					// change rgxp in database
					$db->prepare("UPDATE " . $this->table . " SET rgxp=? WHERE id=?")
					   ->execute('url', $dc->activeRecord->id);
				}
				if ($dc->activeRecord->multiple)
				{
					// reset multiple in database
					$db->prepare("UPDATE " . $this->table . " SET multiple=? WHERE id=?")
					   ->execute('', $dc->activeRecord->id);
				}
				break;
				
			case 'unit':
				if ($dc->activeRecord->maxLength > 200)
				{
					// change maxLength in database
					$db->prepare("UPDATE " . $this->table . " SET maxLength=? WHERE id=?")
					   ->execute(200, $dc->activeRecord->id);
				}
				if ($dc->activeRecord->multiple)
				{
					// change multiple in database
					$db->prepare("UPDATE " . $this->table . " SET multiple=? WHERE id=?")
					   ->execute('', $dc->activeRecord->id);
				}
				break;

		}
		
		return $value;
	}

	/**
	 * Add the type of content pattern
	 *
	 * @param array $arrRow
	 *
	 * @return string
	 */
	public function setMultipleOptions($value, $dc)
	{
		$db = Database::getInstance();
		
		if ($value > 0 && $dc->activeRecord->maxLength > 255/$value-16)
		{
			// change rgxp in database
			$db->prepare("UPDATE " . $this->table . " SET maxLength=? WHERE id=?")
			   ->execute(round(255/$value-16), $dc->activeRecord->id);
		}
		
		return $value;
	}


	/**
	 * Add the type of content pattern
	 *
	 * @param array $arrRow
	 *
	 * @return string
	 */
	public function setSourceOptions($value, $dc)
	{
		$db = Database::getInstance();
		
		if ($value == 'video' || $value == 'audio')
		{
			if (!$dc->activeRecord->multiSource)
			{
				// change multiSource in database
				$db->prepare("UPDATE " . $this->table . " SET multiSource=? WHERE id=?")
				   ->execute(1, $dc->activeRecord->id);
			}
			if ($dc->activeRecord->sortBy != 'html5media')
			{
				// change sortBy in database
				$db->prepare("UPDATE " . $this->table . " SET sortBy=? WHERE id=?")
				   ->execute('html5media', $dc->activeRecord->id);
			}
			if ($dc->activeRecord->canChangeSortBy)
			{
				// change canChangeSortBy in database
				$db->prepare("UPDATE " . $this->table . " SET canChangeSortBy=? WHERE id=?")
				   ->execute(0, $dc->activeRecord->id);
			}
		}
		
		return $value;
	}

	
	/**
	 * Return all content pattern as array
	 *
	 * @return array
	 */
	public function getPatternForContentBlock()
	{
	
		$pattern = array();
		
		foreach ($GLOBALS['TL_CTP'] as $k=>$v)
		{
			foreach (array_keys($v) as $kk)
			{
				$pattern[$k][] = $kk;					
			}
		}
		
		return $pattern;
	}
	
	/**
	 * Return all content pattern as array
	 *
	 * @return array
	 */
	public function getPatternForSubPattern()
	{
	
		$pattern = array();
		
		foreach ($GLOBALS['TL_CTP'] as $k=>$v)
		{
			foreach (array_keys($v) as $kk)
			{
				// exclude pattern not allowed in sub pattern
				if (!in_array($kk, $GLOBALS['TL_CTP_NA']['subpattern']))
				{
					$pattern[$k][] = $kk;					
				}
			}
		}
		
		return $pattern;
	}

	/**
	 * Return all content pattern as array
	 *
	 * @return array
	 */
	public function getPatternForMultiPattern()
	{
	
		$pattern = array();
		
		foreach ($GLOBALS['TL_CTP'] as $k=>$v)
		{
			foreach (array_keys($v) as $kk)
			{
				// exclude pattern not allowed in multigroup
				if (!in_array($kk, $GLOBALS['TL_CTP_NA']['multipattern']))
				{
					$pattern[$k][] = $kk;					
				}
			}
		}
		
		return $pattern;
	}
	

	/**
	 * Return a list of predefined images sizes
	 *
	 * @return array
	 */
	public function defaultSizes($value, $dc)
	{
		
		if ($value == '')
		{			
			return \System::getContainer()->get('contao.image.image_sizes')->getAllOptions()['image_sizes'];
		}

		$intNeededSize = \StringUtil::deserialize($dc->activeRecord->size)[2];
		$arrSelectedSizes = \StringUtil::deserialize($value);
		
		if (!in_array($neededSize, $arrSelectedSizes))
		{
			$arrSelectedSizes[] = $intNeededSize;
			return serialize($arrSelectedSizes);
		}

		return $value;
		
	}
	

	/**
	 * Return all content element templates as array
	 *
	 * @return array
	 */
	public function getRteTemplates()
	{
		return $this->getTemplateGroup('be_tinyMCE');
	}

	

	public function correctGroups ($dc)
	{
		$db = Database::getInstance();
					
		// change predefined groups
		if ($dc->activeRecord->type == 'protection' && !$dc->activeRecord->canChangeGroups)
		{
			// serialize array if not
			$groups = is_array($dc->activeRecord->groups) ? serialize($dc->activeRecord->groups) : $dc->activeRecord->groups;
			
			// save alias to database
			$db->prepare("UPDATE tl_content SET groups=? WHERE type=(SELECT alias FROM tl_content_blocks WHERE id=?)")
			   ->execute($groups, $dc->activeRecord->pid);
		
		}
	}


	public function saveSubPattern ($dc)
	{
		$db = Database::getInstance();
		
		// save changes to subpattern table
		if (in_array($dc->activeRecord->type, $GLOBALS['TL_CTP_SUB']))
		{			
			if ($db->prepare("SELECT * FROM tl_content_subpattern WHERE id=?")->execute($dc->activeRecord->id)->numRows)
			{
				$db->prepare("UPDATE tl_content_subpattern SET pid=?,alias=?,type=?,subPatternType=?,multiPatternMax=? WHERE id=?")
				   ->execute($dc->activeRecord->id, $dc->activeRecord->alias, $dc->activeRecord->type, $dc->activeRecord->subPatternType, $dc->activeRecord->multiPatternMax, $dc->activeRecord->id);
			}
			else
			{
				$db->prepare("INSERT INTO tl_content_subpattern SET id=?,pid=?,alias=?,type=?,subPatternType=?,multiPatternMax=?")
				   ->execute($dc->activeRecord->id, $dc->activeRecord->id, $dc->activeRecord->alias, $dc->activeRecord->type, $dc->activeRecord->subPatternType, $dc->activeRecord->multiPatternMax);
				
			}

		}

		// save the filter for subpattern
		if (isset($GLOBALS['TL_DCA'][$this->table]['list']['sorting']['filter']['suboption'][1]))
		{
			$db->prepare("UPDATE tl_content_pattern SET suboption=? WHERE id=?")
			   ->execute($GLOBALS['TL_DCA'][$this->table]['list']['sorting']['filter']['suboption'][1], $dc->activeRecord->id);
		}
	}

	public function deleteSubPattern ($dc, $intUndoId)
	{
		$db = Database::getInstance();

		// save changes to subpattern table
		if (in_array($dc->activeRecord->type, $GLOBALS['TL_CTP_SUB']))
		{			
			$colPattern = \ContentPatternModel::findByPidAndTable($dc->activeRecord->id, 'tl_content_subpattern');
		
			if ($colPattern === null)
			{
				return;
			}

			// get the undo database row
			$objUndo = $db->prepare("SELECT data FROM tl_undo WHERE id=?")
						  ->execute($intUndoId) ;
				
			$arrData = \StringUtil::deserialize($objUndo->fetchAssoc()[data]);
			

			foreach ($colPattern as $objPattern)
			{
				
				// get value row(s)
				$arrData['tl_content_value'][] = $objPattern->row();

				$objPattern->delete();
			}
		
			// save to the undo database row
			$db->prepare("UPDATE tl_undo SET data=? WHERE id=?")
			   ->execute(serialize($arrData), $intUndoId);
		}
	}



	
	/**
	 * Show a hint if the content block is already in use
	 */
	public function showAlreadyUsedHint($dc)
	{
		if ($_POST || \Input::get('act') != 'edit')
		{
			return;
		}

		// Return if the user cannot access the layout module (see #6190)
		if (!$this->User->hasAccess('themes', 'modules') || !$this->User->hasAccess('layout', 'themes'))
		{
			return;
		}

		// Check if the content block is in use
		$objContentPattern = \ContentPatternModel::findById($dc->id);
		$objContentBlock = \ContentBlocksModel::findById($objContentPattern->pid);
		
		if (\ContentModel::countBy('type', $objContentBlock->alias) > 0)
		{
			\Message::addInfo('Be aware on changes. The content block element is already in use!!');
		}

	}

	
	/**
	 * Return the "toggle visibility" button
	 *
	 * @param array  $row
	 * @param string $href
	 * @param string $label
	 * @param string $title
	 * @param string $icon
	 * @param string $attributes
	 *
	 * @return string
	 */
	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		if (strlen(\Input::get('tid')))
		{
			$this->toggleVisibility(\Input::get('tid'), (\Input::get('state') == 1), (@func_get_arg(12) ?: null));
			$this->redirect($this->getReferer());
		}
		
		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if (!$this->User->hasAccess($this->table . '::invisible', 'alexf'))
		{
			return '';
		}
		
		$href .= '&amp;id='.\Input::get('id').'&amp;tid='.$row['id'].'&amp;state='.$row['invisible'];
		
		if ($row['invisible'])
		{
			$icon = 'invisible.svg';
		}
		
		return '<a href="'.$this->addToUrl($href).'" title="'.\StringUtil::specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label, 'data-state="' . ($row['invisible'] ? 0 : 1) . '"').'</a> ';
	}
	
	
	/**
	 * Toggle the visibility of an element
	 *
	 * @param integer       $intId
	 * @param boolean       $blnVisible
	 * @param DataContainer $dc
	 */
	public function toggleVisibility($intId, $blnVisible, DataContainer $dc=null)
	{
		$db = Database::getInstance();
		
		// Set the ID and action
		\Input::setGet('id', $intId);
		\Input::setGet('act', 'toggle');
		
		if ($dc)
		{
			$dc->id = $intId; // see #8043
		}
		
		if (!$this->User->isAdmin)
		{
			return;
		}		
				
		// Check the field access
		if (!$this->User->hasAccess($this->table . '::invisible', 'alexf'))
		{
			$this->log('Not enough permissions to publish/unpublish content element ID "'.$intId.'"', __METHOD__, TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}
		
		// The onload_callbacks vary depending on the dynamic parent table (see #4894)
		if (is_array($GLOBALS['TL_DCA'][$this->table]['config']['onload_callback']))
		{
			foreach ($GLOBALS['TL_DCA'][$this->table]['config']['onload_callback'] as $callback)
			{
				if (is_array($callback))
				{
					$this->import($callback[0]);
					$this->{$callback[0]}->{$callback[1]}(($dc ?: $this));
				}
				elseif (is_callable($callback))
				{
					$callback(($dc ?: $this));
				}
			}
		}
		
		// Check permissions to publish
		if (!$this->User->hasAccess($this->table . '::invisible', 'alexf'))
		{
			$this->log('Not enough permissions to show/hide content element ID "'.$intId.'"', __METHOD__, TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}
		
		$objVersions = new Versions($this->table, $intId);
		$objVersions->initialize();
		
		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA'][$this->table]['fields']['invisible']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA'][$this->table]['fields']['invisible']['save_callback'] as $callback)
			{
				if (is_array($callback))
				{
					$this->import($callback[0]);
					$blnVisible = $this->{$callback[0]}->{$callback[1]}($blnVisible, ($dc ?: $this));
				}
				elseif (is_callable($callback))
				{
					$blnVisible = $callback($blnVisible, ($dc ?: $this));
				}
			}
		}
		
		// Update the database
		$db->prepare("UPDATE " . $this->table . " SET tstamp=". time() .", invisible='" . ($blnVisible ? '' : 1) . "' WHERE id=?")
		   ->execute($intId);
					   
		$objVersions->create();
		$this->log('A new version of record "' . $this->table . '.id='.$intId.'" has been created'.$this->getParentEntries('tl_content_pattern', $intId), __METHOD__, TL_GENERAL);
	}
	
	
	/**
	 * Get all forms and return them as array
	 *
	 * @return array
	 */
	public function getForms()
	{		
		$arrForms = array();
		$objForms = $this->Database->execute("SELECT id, title FROM tl_form ORDER BY title");
		
		while ($objForms->next())
		{
			$arrForms[$objForms->id] = $objForms->title . ' (ID ' . $objForms->id . ')';
		}
		
		return $arrForms;
	}
	
	/**
	 * Return the edit form wizard
	 *
	 * @param DataContainer $dc
	 *
	 * @return string
	 */
	public function editForm(DataContainer $dc)
	{
		return ($dc->value < 1) ? '' : ' <a href="contao/main.php?do=form&amp;table=tl_form_field&amp;id=' . $dc->value . '&amp;popup=1&amp;nb=1&amp;rt=' . REQUEST_TOKEN . '" title="' . sprintf(\StringUtil::specialchars($GLOBALS['TL_LANG']['tl_content']['editalias'][1]), $dc->value) . '" style="padding-left:3px" onclick="Backend.openModalIframe({\'width\':768,\'title\':\'' . \StringUtil::specialchars(str_replace("'", "\\'", sprintf($GLOBALS['TL_LANG']['tl_content']['editalias'][1], $dc->value))) . '\',\'url\':this.href});return false">' . \Image::getHtml('alias.svg', $GLOBALS['TL_LANG']['tl_content']['editalias'][0]) . '</a>';
	}


	/**
	 * Return all content element templates as array
	 *
	 * @return array
	 */
	public function getFormTemplates()
	{
		return $this->getTemplateGroup('frm_');
	}


	
	/**
	 * Get all modules and return them as array
	 *
	 * @return array
	 */
	public function getModules()
	{
		$arrModules = array();
		$objModules = $this->Database->execute("SELECT m.id, m.name, t.name AS theme FROM tl_module m LEFT JOIN tl_theme t ON m.pid=t.id ORDER BY t.name, m.name");
		
		while ($objModules->next())
		{
			$arrModules[$objModules->theme][$objModules->id] = $objModules->name . ' (ID ' . $objModules->id . ')';
		}
		
		return $arrModules;
	}
	
	/**
	 * Return the edit module wizard
	 *
	 * @param DataContainer $dc
	 *
	 * @return string
	 */
	public function editModule(DataContainer $dc)
	{
		return ($dc->value < 1) ? '' : ' <a href="contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $dc->value . '&amp;popup=1&amp;nb=1&amp;rt=' . REQUEST_TOKEN . '" title="' . sprintf(\StringUtil::specialchars($GLOBALS['TL_LANG']['tl_content']['editalias'][1]), $dc->value) . '" style="padding-left:3px" onclick="Backend.openModalIframe({\'width\':768,\'title\':\'' . \StringUtil::specialchars(str_replace("'", "\\'", sprintf($GLOBALS['TL_LANG']['tl_content']['editalias'][1], $dc->value))) . '\',\'url\':this.href});return false">' . \Image::getHtml('alias.svg', $GLOBALS['TL_LANG']['tl_content']['editalias'][0]) . '</a>';
	}

	
	/**
	 * Return all content element templates as array
	 *
	 * @return array
	 */
	public function getCommentsTemplates()
	{
		return $this->getTemplateGroup('com_');
	}



}
