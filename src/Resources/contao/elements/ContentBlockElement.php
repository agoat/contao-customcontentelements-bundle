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


namespace Agoat\ContentBlocks;

use Contao\System;
use Contao\ContentElement;
use Contao\TemplateLoader;
use Agoat\ContentBlocks\Template;
use Agoat\ContentBlocks\Pattern;


class ContentBlockElement extends ContentElement
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'cb_standard';
	
	
	protected $objElement;

	protected $objBlock;
	

	
	/**
	 * Initialize the object
	 *
	 * @param \ContentModel $objElement
	 * @param string        $strColumn
	 */
	public function __construct($objElement, $strColumn='main')
	{
		if ($objElement instanceof \Model)
		{
			$this->objModel = $objElement;
		}
		elseif ($objElement instanceof \Model\Collection)
		{
			$this->objModel = $objElement->current();
		}
		
		$this->arrData = $objElement->row();
		$this->strColumn = $strColumn;
		
	}	
	

	/**
	 * Prepare the pattern data for the template
	 *
	 * @return string
	 */
	public function generate()
	{		
		// get the content element model object
		$this->objBlock = \ContentBlocksModel::findOneByAlias($this->type);

		if ($this->objBlock === null)
		{
			return;
		}

		// don´t show invisible content block elements
		if ($this->objBlock->invisible)
		{
			System::log('Content block element "'.$this->type.'" with parent record "' . $this->ptable . '.id=' . $this->pid . '" is invisible and will not be shown', __METHOD__, TL_ERROR);
			return;
		}
		
		// register the custom template
		if (!array_key_exists($this->objBlock->template, TemplateLoader::getFiles()))
		{
			TemplateLoader::addFile($this->objBlock->template, $this->objBlock->getRelated('pid')->templates);
		}

		// set the template file
		$this->strTemplate = $this->objBlock->template;
		
		
		// add the contentblocks backend stylesheets (depreciated)
		if (TL_MODE == 'BE')
		{
			$objFile = \FilesModel::findByPk($this->objBlock->stylesheet);
		
			if ($objFile !== null)
			{
				$GLOBALS['TL_CB_CSS'][] = $objFile->path . '|static';
			}
		}
		
		
		// content element output
		if (TL_MODE == 'FE' && !BE_USER_LOGGED_IN && ($this->invisible || ($this->start != '' && $this->start > time()) || ($this->stop != '' && $this->stop < time())))
		{
			return '';
		}

		$this->Template = new Template($this->strTemplate);
		
		// deliver some general element data
		$this->Template->setData(
			array (
				'id' => $this->id,
				'pid' => $this->pid,
				'ptable' => $this->ptable,
				'tstamp' => $this->tstamp,
				'start' => $this->start,
				'stop' => $this->stop,
				'protected' => $this->protected,
				'inColumn' => $this->strColumn
			)
		);

		// compile pattern to prepare values
		$this->compile();
		
		return $this->Template->parse();
	}

	
	/**
	 * Generate content element
	 */
	protected function compile()
	{		
		// get the pattern model collection
		$colPattern = \ContentPatternModel::findPublishedByPidAndTable($this->objBlock->id, 'tl_content_blocks');

		if ($colPattern === null)
		{
			return;
		}
		
		// get correct element id (included content element) see #37
		$intCid = ($this->origId) ? $this->origId : $this->id;
		
		// get values for content block
		$colValues = \ContentValueModel::findByCid($intCid);

		if ($colValues !== null)
		{
			foreach ($colValues as $objValue)
			{
				$arrValues[$objValue->pid][$objValue->rid] = $objValue;
			}							
		}
		
		
		// prepare values for every pattern
		foreach($colPattern as $objPattern)
		{
			// don´t show the invisible or system pattern
			if (in_array($objPattern->type, $GLOBALS['TL_SYS_PATTERN']))
			{
				continue;
			}

			
			$strClass = Pattern::findClass($objPattern->type);
				
			if (!class_exists($strClass))
			{
				System::log('Pattern element class "'.$strClass.'" (pattern element "'.$objPattern->type.'") does not exist', __METHOD__, TL_ERROR);
			}
			else
			{
				$objPatternClass = new $strClass($objPattern);
				$objPatternClass->cid = $intCid;
				$objPatternClass->rid = 0;
				$objPatternClass->Template = $this->Template;
				$objPatternClass->arrValues = $arrValues;
				$objPatternClass->Value = $arrValues[$objPattern->id][0];
				
				$objPatternClass->compile();
			}
		}
	}
}
