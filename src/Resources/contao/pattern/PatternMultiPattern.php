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

use Contao\StringUtil;
use Agoat\ContentBlocks\Pattern;


class PatternMultiPattern extends Pattern
{

	
	/**
	 * generate the DCA construct
	 */
	public function construct()
	{

		// get count from tl_content_value
			
			// for now take the maxCount value
			for ($rid=0; $rid < $this->maxCount; $rid++)
			{
				// add multistart widget (with add, delete and move buttons)
				
				// add the multi pattern to palettes
				$colMultiPattern = \ContentPatternModel::findPublishedByPidAndTable($this->id, 'tl_content_subpattern', array('order'=>'sorting ASC'));
				
				if ($colMultiPattern === null)
				{
					return;
				}


				foreach($colMultiPattern as $objMultiPattern)
				{
					// construct dca for pattern
					$strClass = \Agoat\ContentBlocks\Pattern::findClass($objMultiPattern->type);
						
					if (!class_exists($strClass))
					{
						\System::log('Pattern element class "'.$strClass.'" (pattern element "'.$objMultiPattern->type.'") does not exist', __METHOD__, TL_ERROR);
					}
					else
					{
						$objPatternClass = new $strClass($objMultiPattern);
						$objPatternClass->cid = $this->cid;
						$objPatternClass->rid = ($this->rid * 100) + $rid;
						$objPatternClass->alias = $this->alias;			
						
						$objPatternClass->construct();
					}				
				}
				
				
				// add multiend widget
			}
			
		
	}


	/**
	 * prepare a field view for the backend
	 *
	 * @param array $arrAttributes An optional attributes array
	 */
	public function view()
	{
		//return '<div class="tl_checkbox_single_container"><input class="tl_checkbox" value="1" type="checkbox"> <label>' . $this->label . '</label><p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';	
	}


	/**
	 * prepare the values for the frontend template
	 *
	 * @param array $arrAttributes An optional attributes array
	 */	
	public function compile()
	{
		// get the pattern model collection
		$colPattern = \ContentPatternModel::findPublishedByPidAndTable($this->id, 'tl_content_subpattern');

		if ($colPattern === null)
		{
			return;
		}
/*
		// get values for content block
		$colValues = \ContentValueModel::findByCid($this->cid);

		if ($colValues !== null)
		{
			foreach ($colValues as $objValue)
			{
				$arrValues[$objValue->pid][$objValue->rid] = $objValue;
			}
		}
*/		

		// add new alias to the value mapper
		$this->arrMapper[] = $this->alias;


		// get count from tl_content_value

		for ($rid=0; $rid < $this->maxCount; $rid++)
		{
		
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
					$objPatternClass->cid = $this->cid;
					$objPatternClass->rid = ($this->rid * 100) + $rid;
					$objPatternClass->Template = $this->Template;
					$objPatternClass->arrMapper = array_merge($this->arrMapper, array($rid));
					$objPatternClass->arrValues = $this->arrValues;
					$objPatternClass->Value = $this->arrValues[$objPattern->id][($this->rid * 100) + $rid];
					
					$objPatternClass->compile();
				
				}
			}
		}
			
	}
}
