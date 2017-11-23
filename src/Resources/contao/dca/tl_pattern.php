<?php

/*
 * Custom content elements extension for Contao Open Source CMS.
 *
 * @copyright  Arne Stappen (alias aGoat) 2017
 * @package    contao-contentelements
 * @author     Arne Stappen <mehh@agoat.xyz>
 * @link       https://agoat.xyz
 * @license    LGPL-3.0
 */

 
/**
 * Table tl_pattern
 */
$GLOBALS['TL_DCA']['tl_pattern'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'switchToEdit'                => false,
		'enableVersioning'            => true,
		'ptable'                      => 'tl_elements',
		'ctable'                      => array('tl_subpattern'),
		'dynamicPtable'				  => true,
		'onload_callback' => array
		(
			//array('tl_pattern', 'checkPermission'),
			array('tl_pattern', 'showAlreadyUsedHint')
		),
		'onsubmit_callback'			  => array
		(
			array('tl_pattern', 'saveMemberGroups'),
			array('tl_pattern', 'saveSubPattern'),
		),
		'oncopy_callback'			  => array
		(
			array('tl_pattern', 'copySubPattern'),
		),
		'oncut_callback'			  => array
		(
			array('tl_pattern', 'cutSubPattern'),
		),
		'ondelete_callback'			  => array
		(
			array('tl_pattern', 'deleteSubPattern'),
		),
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'pid,ptable,sorting' => 'index',
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
			'child_record_callback'   => array('tl_pattern', 'previewElementPattern')
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
				'label'               => &$GLOBALS['TL_LANG']['tl_pattern']['edit'],
				'icon'                => 'edit.svg',
				'button_callback'     => array('tl_pattern', 'patternButton')
			),
			'editheader' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pattern']['editheader'],
				'href'                => 'act=edit',
				'icon'                => 'header.svg',
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pattern']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.svg',
				'attributes'          => 'onclick="Backend.getScrollOffset()"'
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pattern']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.svg',
				'attributes'          => 'onclick="Backend.getScrollOffset()"'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pattern']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.svg',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pattern']['toggle'],
				'icon'                => 'visible.svg',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_pattern', 'toggleIcon')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_pattern']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.svg'
			)
		)
	),
	// Palettes
	'palettes' => array
	(
		'__selector__'                => array('type', 'source', 'multiSource', 'insideRoot', 'picker' ,'subPatternType'),
		'default'                     => '{type_legend},type',
		// input
		'textfield'					  => '{type_legend},type;{textfield_legend},minLength,maxLength,rgxp,defaultValue,multiple,picker;{label_legend},label,description;{pattern_legend},alias,mandatory,classClr,classLong;{invisible_legend},invisible',
		'textarea'					  => '{type_legend},type;{textarea_legend},rteTemplate;{label_legend},label,description;{pattern_legend},alias,mandatory,;{invisible_legend},invisible',
		'code'					 	  => '{type_legend},type;{code_legend},highlight,canChangeHighlight,htmlspecialchars;{label_legend},label,description;{pattern_legend},alias,mandatory,;{invisible_legend},invisible',
		'selectfield'				  => '{type_legend},type;{select_legend},options,blankOption,multiSelect;{label_legend},label,description;{pattern_legend},alias,mandatory,classClr;{invisible_legend},invisible',
		'checkbox'					  => '{type_legend},type;{label_legend},label,description;{pattern_legend},alias,mandatory,classClr;{invisible_legend},invisible',
		'listwizard'				  => '{type_legend},type;{label_legend},label,description;{pattern_legend},alias,mandatory;{invisible_legend},invisible',
		'tablewizard'				  => '{type_legend},type;{label_legend},label,description;{pattern_legend},alias,mandatory;{invisible_legend},invisible',
		'articletree'				  => '{type_legend},type;{article_legend},multiArticle,insideRoot;{label_legend},label,description;{pattern_legend},alias,mandatory,classClr;{invisible_legend},invisible',
		'pagetree'					  => '{type_legend},type;{page_legend},multiPage,insideRoot;{label_legend},label,description;{pattern_legend},alias,mandatory,classClr;{invisible_legend},invisible',
		'filetree'					  => '{type_legend},type;{source_legend},source;{multiSource_legend},multiSource;{label_legend},label,description;{pattern_legend},alias,mandatory,classClr;{invisible_legend},invisible',
		'imagesize'					  => '{type_legend},type;{source_legend},size;{pattern_legend},alias;{invisible_legend},invisible',
		// layout
		'section'					  => '{type_legend},type;{section_legend},label,hidden;{invisible_legend},invisible',
		'explanation'				  => '{type_legend},type;{explanation_legend},explanation;{invisible_legend},invisible',
		'subpattern'				  => '{type_legend},type;{subpattern_legend},subPatternType;{label_legend},label,description;{pattern_legend},alias;{invisible_legend},invisible',
		'multipattern'				  => '{type_legend},type;{multipattern_legend},numberOfGroups;{label_legend},label,description;{pattern_legend},alias;{invisible_legend},invisible',
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
		'insideRoot'				  => 'insideLang',
		'picker_unit'				  => 'units',
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
			'sql'                     => "varchar(64) NOT NULL default 'tl_elements'"
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
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['type'],
			'default'                 => 'textfield',
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'select',
			'options_callback'        => array('tl_pattern', 'getPattern'),
			'reference'               => &$GLOBALS['TL_LANG']['CTP'],
			'eval'                    => array('helpwizard'=>true, 'chosen'=>true, 'submitOnChange'=>true, 'tl_class'=>'w50'),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'alias' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['alias'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'rgxp'=>'folderalias', 'maxlength'=>64, 'tl_class'=>'w50'),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'invisible' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['invisible'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'label' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['label'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
			'sql'                     => "varchar(128) NOT NULL default ''"
		),
		'description' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['description'],
			'exclude'                 => true,
			'inputType'               => 'textarea',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'long clr'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'mandatory' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['mandatory'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'classLong' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['classLong'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'classClr' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['classClr'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'hidden' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['hidden'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'subPatternType' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['subPatternType'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'                 => array('button', 'options'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_pattern_subPatternType'],
			'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'w50'),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'numberOfGroups' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['numberOfGroups'],
			'exclude'                 => true,
			'default'				  => 100,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'natural', 'maxlength'=>3, 'minval'=>2, 'maxval'=>100, 'tl_class'=>'w50 clr'),
			'sql'                     => "smallint(5) unsigned NOT NULL default '0'"
		),
		'explanation' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['explanation'],
			'exclude'                 => true,
			'inputType'               => 'textarea',
			'eval'                    => array('mandatory'=>true, 'rte'=>'tinyExplanation'),
			'sql'                     => "mediumtext NULL"
		),
		'style' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['style'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50 clr'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'canChangeStart' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['canChangeStart'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12 clr'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'canChangeStop' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['canChangeStop'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'groups' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['groups'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'foreignKey'              => 'tl_member_group.name',
			'eval'                    => array('mandatory'=>true, 'multiple'=>true),
			'sql'                     => "blob NULL",
			'relation'                => array('type'=>'hasMany', 'load'=>'lazy')
		),
		'canChangeGroups' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['canChangeGroups'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),

		'rteTemplate' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['rteTemplate'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'default'				  => 'be_tinyMCE_standard',
			'flag'                    => 11,
			'options_callback'        => array('tl_pattern', 'getRteTemplates'),
			'eval'                    => array('tl_class'=>'w50'),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),

		'highlight' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['highlight'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'                 => array('HTML', 'HTML5', 'XML', 'JavaScript', 'CSS', 'SCSS', 'PHP', 'JSON', 'Markdown'),
			'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		'canChangeHighlight' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['canChangeHighlight'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'htmlspecialchars' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['htmlspecialchars'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),

		'source' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['source'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'                 => array('all', 'image', 'video', 'audio', 'custom'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_pattern_source'],
			'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'w50 clr'),
			'save_callback' => array
			(
				array ('tl_pattern','setSourceOptions')
			),
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		'customExtension' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['customExtension'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),

		'size' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['size'],
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
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['canChangeSize'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'sizeList' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['sizeList'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'eval'                    => array('multiple'=>true, 'includeBlankOption'=>true, 'size'=>10, 'tl_class'=>'w50 clr', 'chosen'=>true),
			'options_callback' => function ()
			{
				return System::getContainer()->get('contao.image.image_sizes')->getAllOptions()['image_sizes'];
			},
			'load_callback' => array
			(
				array ('tl_pattern','defaultSizes')
			),
			'save_callback' => array
			(
				array ('tl_pattern','defaultSizes')
			),
			'sql'                     => "blob NULL"		
		),
		'canEnterSize' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['canEnterSize'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),

		'multiSource' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['multiSource'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'w50 m12 clr'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'canSelectFolder' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['canSelectFolder'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12 clr'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'multiPage' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['multiPage'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'multiArticle' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['multiArticle'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'insideRoot' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['insideRoot'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'insideLang' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['insideLang'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'sortBy' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['sortBy'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'                 => array('custom', 'name_asc', 'name_desc', 'date_asc', 'date_desc', 'random', 'html5media'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_pattern_sortby'],
			'eval'                    => array('tl_class'=>'w50 clr'),
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		'canChangeSortBy' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['canChangeSortBy'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'metaIgnore' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['metaIgnore'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'numberOfItems' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['numberOfItems'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'natural', 'tl_class'=>'w50 clr'),
			'sql'                     => "smallint(5) unsigned NOT NULL default '0'"
		),

		'minLength' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['minLength'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'default'                 => '0',
			'eval'                    => array('rgxp'=>'natural', 'maxlength'=>3, 'maxval'=>255, 'tl_class'=>'w50'),
			'sql'                     => "smallint(5) unsigned NOT NULL default '255'"
		),
		'maxLength' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['maxLength'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'default'                 => '255',
			'eval'                    => array('rgxp'=>'natural', 'maxlength'=>3, 'maxval'=>255, 'tl_class'=>'w50'),
			'sql'                     => "smallint(5) unsigned NOT NULL default '255'"
		),
		'rgxp' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['rgxp'],
			'inputType'               => 'select',
			'options'                 => array('natural', 'prcnt', 'digit', 'alpha', 'alnum', 'extnd', 'date', 'time', 'datim', 'phone', 'email', 'url'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_pattern'],
			'eval'                    => array('helpwizard'=>true, 'includeBlankOption'=>true, 'tl_class'=>'w50'),
			'sql'                     => "varchar(16) NOT NULL default ''"
		),
		'defaultValue' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['defaultValue'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'multiple' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['multiple'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'				  => array(2, 3, 4),
			'eval'                    => array('submitOnChange'=>true, 'includeBlankOption'=>true, 'tl_class'=>'w50'),
			'save_callback' => array
			(
				array ('tl_pattern','setMultipleOption')
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'picker' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['picker'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'				  => array('datetime', 'color', 'link', 'unit'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_pattern'],
			'eval'                    => array('submitOnChange'=>true, 'includeBlankOption'=>true, 'tl_class'=>'w50'),
			'save_callback' => array
			(
				array ('tl_pattern','setPickerOptions')
			),
			'sql'                     => "varchar(16) NOT NULL default ''"
		),
		'units' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['units'],
			'exclude'                 => true,
			'inputType'               => 'optionWizard',
			'eval'                    => array('allowHtml'=>true, 'tl_class'=>'clr'),
			'sql'                     => "blob NULL"
		),
		'options' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['options'],
			'exclude'                 => true,
			'inputType'               => 'optionWizard',
			'eval'                    => array('allowHtml'=>true, 'tl_class'=>'clr'),
			'sql'                     => "blob NULL"
		),
		'blankOption' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['blankOption'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'multiSelect' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['multiSelect'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'form' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['form'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options_callback'        => array('tl_pattern', 'getForms'),
			'eval'                    => array('mandatory'=>true, 'chosen'=>true, 'submitOnChange'=>true, 'tl_class'=>'w50 wizard'),
			'wizard' => array
			(
				array('tl_pattern', 'editForm')
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'module' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_pattern']['module'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options_callback'        => array('tl_pattern', 'getModules'),
			'eval'                    => array('mandatory'=>true, 'chosen'=>true, 'submitOnChange'=>true, 'tl_class'=>'w50 wizard'),
			'wizard' => array
			(
				array('tl_pattern', 'editModule')
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),

	)
);


/**
 * Provide miscellaneous methods that are used by the data configuration array.
 */
class tl_pattern extends Backend
{

	use Agoat\CustomContentElementsBundle\Contao\Pattern;
	
	/**
	 * Database table name
	 * @var string
	 */
	protected $table = 'tl_pattern';
	
	
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}


	/**
	 * Generate a sub pattern filter
	 *
	 * @param object $dc
	 *
	 * @return string
	 */
	public function generatesubPatternFilter($dc) 
	{ 
		$objPattern = \PatternModel::findByPk(\Input::get('spid'));
		
		if ($objPattern !== null)
		{
			$return = '<div class="tl_filter tl_subpanel"><strong>' . $GLOBALS['TL_LANG']['tl_pattern']['suboption'] . ' </strong>';
			$return .= '<select name="suboption" id="suboption" class="tl_select" onchange="this.form.submit()">';

			foreach (StringUtil::deserialize($objPattern->options) as $arrOption)
			{
				if ($arrOption['group'])
				{
					if ($blnOpenGroup)
					{
						$return .= '</optgroup>';
					}
					
					$return .= '<optgroup label="&nbsp;' . StringUtil::specialchars($arrOption['label']) . '">';
					$blnOpenGroup = true;
					continue;
				}
				
				$return .='<option value="' . $arrOption['value'] . '"' . (($GLOBALS['TL_DCA']['tl_pattern']['list']['sorting']['filter']['suboption'][1] == $arrOption['value'])? ' selected' : '') . '>' . StringUtil::specialchars($arrOption['label']) . '</option>';
			}

			if ($blnOpenGroup)
			{
				$return .= '</optgroup>';
			}

			$return .='</select></div>';
		
			return $return;
		}
	}

	
	/**
	 * Sub pattern filter
	 *
	 * @param object $dc
	 */
	public function subPatternFilter($dc) 
	{ 
		/** @var SessionInterface $objSession */
		$objSession = \System::getContainer()->get('session');
		
		/** @var AttributeBagInterface $objSessionBag */
		$objSessionBag = $objSession->getBag('contao_backend');
		
		$filter = $objSessionBag->get('filter');
		$suboption = $filter['tl_pattern_'.CURRENT_ID]['suboption'];

		$objPattern = \PatternModel::findByPk(\Input::get('spid'));

		if ($objPattern === null)
		{
			// Try from pid when no spid
			$objPattern = \PatternModel::findByPk(\Input::get('pid'));
		}

		$arrAllowedValues = array();
		
		if ($objPattern->options !== null)
		{
			foreach (StringUtil::deserialize($objPattern->options) as $arrOption)
			{
				if (!$arrOption['group'])
				{
					$arrAllowedValues[] = $arrOption['value'];
				}
			}
		}

		if (\Input::post('FORM_SUBMIT') == 'tl_filters' && in_array(\Input::post('suboption'), $arrAllowedValues))
		{
			// Validate the user input
			if (\Input::post('suboption'))
			{
				$suboption = \Input::Post('suboption');
			}
			
			$filter['tl_pattern_'.CURRENT_ID]['suboption'] = $suboption;
			$objSessionBag->set('filter', $filter);
		}
		
		if (!$suboption)
		{
			
			if ($objPattern !== null)
			{
				foreach (StringUtil::deserialize($objPattern->options) as $arrOption)
				{
					if ($arrOption['default'] || (!$suboption && !$arrOption['group']))
					{
						$suboption = $arrOption['value'];
					}	
				}
				
			$filter['tl_pattern_'.CURRENT_ID]['suboption'] = $suboption;
			$objSessionBag->set('filter', $filter);
			}
		}

		// Set the filter option
		$GLOBALS['TL_DCA']['tl_pattern']['list']['sorting']['filter']['suboption'] = array('suboption=?', $suboption);
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
		if (\Agoat\ContentElements\Pattern::isSubPattern($row['type']))
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

		// Prepare option string (alias / replicable)
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
		
		// Get pattern model and call view method
		$objPattern = new \PatternModel();
		$objPattern->setRow($arrRow);
		
		$strClass = Pattern::findClass($objPattern->type);
				
		if (!class_exists($strClass))
		{
			static::log('Pattern element class "'.$strClass.'" (pattern element "'.$objPattern->type.'") does not exist', __METHOD__, TL_ERROR);
		}
		else
		{
			$objPatternClass = new $strClass($objPattern);	
			$strPatternView = $objPatternClass->preview();
		}
		
		return '<div class="cte_type ' . $key . '">' . $type . $options . '</div><div style="padding: 5px 0 10px;">' . $strPatternView . '</div>';
	}

	
	/**
	 * Adjust maxlength and multiple settings according to the rgxp settings
	 *
	 * @param mixed         $value
	 * @param DataContainer $dc
	 *
	 * @return mixed
	 */
	public function setPickerOptions($value, $dc)
	{
		$db = Database::getInstance();
		
		switch ($value)
		{
			case 'datetime':
				if (!in_array($dc->activeRecord->rgxp, array('date', 'time', 'datim')))
				{
					// Change rgxp in database
					$db->prepare("UPDATE " . $this->table . " SET rgxp=? WHERE id=?")
					   ->execute('date', $dc->activeRecord->id);
				}
				if ($dc->activeRecord->multiple)
				{
					// Reset multiple in database
					$db->prepare("UPDATE " . $this->table . " SET multiple=? WHERE id=?")
					   ->execute('', $dc->activeRecord->id);
				}
				break;
	
			case 'color':
				if (!in_array($dc->activeRecord->rgxp, array('alnum', 'extnd')))
				{
					// Change rgxp in database
					$db->prepare("UPDATE " . $this->table . " SET rgxp=? WHERE id=?")
					   ->execute('', $dc->activeRecord->id);
				}
				break;
				
			case 'link':
				if ($dc->activeRecord->rgxp != 'url')
				{
					// Change rgxp in database
					$db->prepare("UPDATE " . $this->table . " SET rgxp=? WHERE id=?")
					   ->execute('url', $dc->activeRecord->id);
				}
				if ($dc->activeRecord->multiple)
				{
					// Reset multiple in database
					$db->prepare("UPDATE " . $this->table . " SET multiple=? WHERE id=?")
					   ->execute('', $dc->activeRecord->id);
				}
				break;
				
			case 'unit':
				if ($dc->activeRecord->maxLength > 200)
				{
					// Change maxLength in database
					$db->prepare("UPDATE " . $this->table . " SET maxLength=? WHERE id=?")
					   ->execute(200, $dc->activeRecord->id);
				}
				if ($dc->activeRecord->multiple)
				{
					// Change multiple in database
					$db->prepare("UPDATE " . $this->table . " SET multiple=? WHERE id=?")
					   ->execute('', $dc->activeRecord->id);
				}
				break;
	
		}
		
		return $value;
	}


	/**
	 * Reduce the maxLength for 4 input fields
	 *
	 * @param mixed         $value
	 * @param DataContainer $dc
	 *
	 * @return mixed
	 */
	public function setMultipleOption($value, $dc)
	{
		$db = Database::getInstance();
		
		if ($value > 3 && $dc->activeRecord->maxLength > 200)
		{
			// change maxLegth in database
			$db->prepare("UPDATE " . $this->table . " SET maxLength=? WHERE id=?")
			   ->execute(200, $dc->activeRecord->id);
		}
		
		return $value;
	}


	/**
	 * Adjust multiSource and sortBy according to the source type
	 *
	 * @param mixed         $value
	 * @param DataContainer $dc
	 *
	 * @return mixed
	 */
	public function setSourceOptions($value, $dc)
	{
		$db = Database::getInstance();
		
		if ($value == 'video' || $value == 'audio')
		{
			if (!$dc->activeRecord->multiSource)
			{
				// Change multiSource in database
				$db->prepare("UPDATE " . $this->table . " SET multiSource=? WHERE id=?")
				   ->execute(1, $dc->activeRecord->id);
			}
			if ($dc->activeRecord->sortBy != 'html5media')
			{
				// Change sortBy in database
				$db->prepare("UPDATE " . $this->table . " SET sortBy=? WHERE id=?")
				   ->execute('html5media', $dc->activeRecord->id);
			}
			if ($dc->activeRecord->canChangeSortBy)
			{
				// Change canChangeSortBy in database
				$db->prepare("UPDATE " . $this->table . " SET canChangeSortBy=? WHERE id=?")
				   ->execute(0, $dc->activeRecord->id);
			}
		}
		
		return $value;
	}

	
	/**
	 * Return content pattern as array
	 *
	 * @param DataContainer $dc
	 *	 
	 * @return array
	 */
	public function getPattern($dc)
	{
		$pattern = array();

		if ($dc->activeRecord->ptable != 'tl_elements')
		{
			$objParent = \PatternModel::findById($dc->activeRecord->pid);
		}
			
		foreach ($GLOBALS['TL_CTP'] as $k=>$v)
		{
			foreach ($v as $kk=>$vv)
			{
				if ($objParent === null)
				{
					if (!$vv['unique'])
					{
						$pattern[$k][] = $kk;
					}
					
					elseif (\PatternModel::countByPidAndType($dc->activeRecord->pid, $kk) < 1 || $dc->activeRecord->type == $kk)
					{
						$pattern[$k][] = $kk;
					}
				}
				
				elseif (is_array($vv['childOf']) && in_array($objParent->type, $vv['childOf']))
				{
					$pattern[$k][] = $kk;
				}
			}
		}
	
		return $pattern;
	}
	
	

	/**
	 * Return a (reduced) list of predefined images sizes
	 *
	 * @param mixed         $value
	 * @param DataContainer $dc
	 *
	 * @return mixed
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
	 * Return tinyMCE templates as array
	 *
	 * @return array
	 */
	public function getRteTemplates()
	{
		return $this->getTemplateGroup('be_tinyMCE');
	}
	

	/**
	 * Save group settings from the pattern to the content element
	 *
	 * @param DataContainer $dc
	 */
	public function saveMemberGroups ($dc)
	{
		$db = Database::getInstance();
					
		// Change predefined groups
		if ($dc->activeRecord->type == 'protection' && !$dc->activeRecord->canChangeGroups)
		{
			// Serialize array if not
			$groups = is_array($dc->activeRecord->groups) ? serialize($dc->activeRecord->groups) : $dc->activeRecord->groups;
			
			// Save alias to database
			$db->prepare("UPDATE tl_content SET groups=? WHERE type=(SELECT alias FROM tl_elements WHERE id=?)")
			   ->execute($groups, $dc->activeRecord->pid);
		}
	}

	
	/**
	 * Save subpattern rows with the pattern
	 *
	 * @param DataContainer $dc
	 */
	public function saveSubPattern ($dc)
	{
		$db = Database::getInstance();
		
		// Save changes to subpattern table
		if (\Agoat\ContentElements\Pattern::isSubPattern($dc->activeRecord->type))
		{			
			if ($db->prepare("SELECT * FROM tl_subpattern WHERE id=?")->execute($dc->activeRecord->id)->numRows)
			{
				$db->prepare("UPDATE tl_subpattern SET pid=?,title=?,alias=?,type=?,subPatternType=?,numberOfGroups=? WHERE id=?")
				   ->execute($dc->activeRecord->id, $dc->activeRecord->label, $dc->activeRecord->alias, $dc->activeRecord->type, $dc->activeRecord->subPatternType, $dc->activeRecord->numberOfGroups, $dc->activeRecord->id);
			}
			else
			{
				$db->prepare("INSERT INTO tl_subpattern SET id=?,pid=?,title=?,alias=?,type=?,subPatternType=?,numberOfGroups=?")
				   ->execute($dc->activeRecord->id, $dc->activeRecord->id, $dc->activeRecord->label, $dc->activeRecord->alias, $dc->activeRecord->type, $dc->activeRecord->subPatternType, $dc->activeRecord->numberOfGroups);
			}
		}

		// Save the filter for subpattern
		if (isset($GLOBALS['TL_DCA'][$this->table]['list']['sorting']['filter']['suboption'][1]))
		{
			$db->prepare("UPDATE tl_pattern SET suboption=? WHERE id=?")
			   ->execute($GLOBALS['TL_DCA'][$this->table]['list']['sorting']['filter']['suboption'][1], $dc->activeRecord->id);
		}
	}

	
	/**
	 * Copy subpattern rows with the pattern
	 *
	 * @param interger      $insertID
	 * @param DataContainer $dc
	 */
	public function copySubPattern ($insertID, $dc)
	{
		$db = Database::getInstance();
		
		$objPattern = \PatternModel::findById($insertID);

		// Copy changes to subpattern table and duplicate the subpattern
		if (\Agoat\ContentElements\Pattern::isSubPattern($objPattern->type))
		{			
			// Copy to subpattern table
			$db->prepare("INSERT INTO tl_subpattern SET id=?,pid=?,title=?,alias=?,type=?,subPatternType=?,numberOfGroups=?")
			   ->execute($objPattern->id, $objPattern->id, $objPattern->label, $objPattern->alias, $objPattern->type, $objPattern->subPatternType, $objPattern->numberOfGroups);

			$colPattern = \PatternModel::findByPidAndTable($dc->id, 'tl_subpattern');
			
			if ($colPattern !== null)
			{
				$arrPattern = $colPattern->fetchAll();
				
				foreach ($arrPattern as $k=>$v)
				{
					$arrPattern[$k]['pid'] = $insertID;
				}				
			}

			while (!empty($arrPattern))
			{
				$arrCurrent = array_shift($arrPattern);
				
				$arrValues = $arrCurrent;
				
				// Remove id
				unset($arrValues['id']);
				
				$objInsertStmt = $db->prepare("INSERT INTO tl_pattern %s")
									->set($arrValues)
									->execute();
				
				$insertID = $objInsertStmt->insertId;
				
				if (\Agoat\ContentElements\Pattern::isSubPattern($arrCurrent['type']))
				{
					// Copy to subpattern table
					$db->prepare("INSERT INTO tl_subpattern SET id=?,pid=?,title=?,alias=?,type=?,subPatternType=?,numberOfGroups=?")
					   ->execute($insertID, $insertID, $arrCurrent['label'], $arrCurrent['alias'], $arrCurrent['type'], $arrCurrent['subPatternType'], $arrCurrent['numberOfGroups']);

					   $colSubPattern = \PatternModel::findByPidAndTable($arrCurrent['id'], 'tl_subpattern');
					
					if ($colSubPattern !== null)
					{
						$arrSubPattern = $colSubPattern->fetchAll();
						
						foreach ($arrSubPattern as $k=>$v)
						{
							$arrSubPattern[$k]['pid'] = $insertID;
						}				
					}
						
					foreach ($arrSubPattern as $k=>$v)
					{
						$arrSubPattern[$k]['pid'] = $insertID;
					}

					$arrPattern = array_merge($arrPattern, $arrSubPattern);
				}
			}
		}
	}
	
	
	/**
	 * Cut/Move subpattern rows with the pattern
	 *
	 * @param DataContainer $dc
	 */
	public function cutSubPattern ($dc)
	{
		$db = Database::getInstance();
	
		// save the filter for subpattern
		if (isset($GLOBALS['TL_DCA'][$this->table]['list']['sorting']['filter']['suboption'][1]))
		{
			$db->prepare("UPDATE tl_pattern SET suboption=? WHERE id=?")
			   ->execute($GLOBALS['TL_DCA'][$this->table]['list']['sorting']['filter']['suboption'][1], $dc->id);
		}
	}
	

	/**
	 * Delete subpattern rows with the pattern
	 *
	 * @param DataContainer $dc
	 * @param interger      $intUndoId
	 */
	public function deleteSubPattern ($dc, $intUndoId)
	{
		$db = Database::getInstance();
		
		if (\Agoat\ContentElements\Pattern::isSubPattern($dc->activeRecord->type))
		{			
			// Get the undo database row
			$objUndo = $db->prepare("SELECT data FROM tl_undo WHERE id=?")
						  ->execute($intUndoId);

			$arrData = \StringUtil::deserialize($objUndo->fetchAssoc()[data]);
			
			$colPattern = \PatternModel::findByPidAndTable($dc->activeRecord->id, 'tl_subpattern');
			
			if ($colPattern !== null)
			{
				$arrPattern = $colPattern->fetchAll();
			}

			while (!empty($arrPattern))
			{
				$arrCurrent = array_shift($arrPattern);
				
				// Add row to undo array
				$arrData['tl_pattern'][] = $arrCurrent;
				
				// Delete row in database
				$db->prepare("DELETE FROM tl_pattern WHERE id=?")
				   ->execute($arrCurrent['id']);
				
				if (\Agoat\ContentElements\Pattern::isSubPattern($arrCurrent['type']))
				{
					// Add related row to undo array
					$arrData['tl_subpattern'][] = $db->prepare("SELECT * FROM tl_subpattern WHERE id=?")
															 ->execute($arrCurrent['id'])->fetchAssoc();
					
					// Delete row in database
					$db->prepare("DELETE FROM tl_subpattern WHERE id=?")
					   ->execute($arrCurrent['id']);
					
					$colSubPattern = \PatternModel::findByPidAndTable($arrCurrent['id'], 'tl_subpattern');
					
					if ($colSubPattern !== null)
					{
						$arrPattern = array_merge($arrPattern, $colSubPattern->fetchAll());
					}
				}
			}
			
			// Save to the undo database row
			$db->prepare("UPDATE tl_undo SET data=? WHERE id=?")
			   ->execute(serialize($arrData), $intUndoId);
		}
	}

	
	/**
	 * Show a hint if the content block is already in use
	 *
	 * @param DataContainer $dc
	 */
	public function showAlreadyUsedHint($dc)
	{
		if ($GLOBALS['_POST'] || \Input::get('act') != 'edit')
		{
			return;
		}

		// Return if the user cannot access the layout module (see #6190)
		if (!$this->User->hasAccess('themes', 'modules') || !$this->User->hasAccess('layout', 'themes'))
		{
			return;
		}

		// Check if the content block is in use
		$objPattern = \PatternModel::findById($dc->id);
		
		while ($objPattern->ptable != 'tl_elements')
		{
			$objPattern = \PatternModel::findById($objPattern->pid);
		}
		
		$objElement = \ElementsModel::findById($objPattern->pid);
		
		if (\ContentModel::countBy('type', $objElement->alias) > 0)
		{
			\Message::addInfo($GLOBALS['TL_LANG']['MSC']['elementInUse']);
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
		$this->log('A new version of record "' . $this->table . '.id='.$intId.'" has been created'.$this->getParentEntries('tl_pattern', $intId), __METHOD__, TL_GENERAL);
	}
	
	
	/**
	 * Get forms and return them as array
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
	 * Return form templates as array
	 *
	 * @return array
	 */
	public function getFormTemplates()
	{
		return $this->getTemplateGroup('frm_');
	}

	
	/**
	 * Get modules and return them as array
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
	 * Return comment templates as array
	 *
	 * @return array
	 */
	public function getCommentsTemplates()
	{
		return $this->getTemplateGroup('com_');
	}
}
