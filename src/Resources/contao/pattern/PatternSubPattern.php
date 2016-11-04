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


class PatternSubPattern extends Pattern
{

	
	/**
	 * generate the DCA construct
	 */
	public function construct()
	{

		if ($this->subPatternType == 'options')
		{
			
			foreach (StringUtil::deserialize($this->options) as $arrOption)
			{
				
				$arrOptions['v'.$arrOption['value']] = $arrOption['label'];
			}
		
		
			// no groups 
			if (count($arrOptions) < 2)
			{
				$arrOptions = $arrOptions['default'];
			}
			
			// generate DCA
			$this->generateDCA('selectField', array
			(
				'inputType' 	=>	'select',
				'label'			=>	array($this->label, $this->description),
				'options'		=>	$arrOptions,
				'eval'			=>	array
				(
					'tl_class'			=>	'clr',
					'onchange'			=>	'Backend.autoSubmit(\'tl_content\')',
				)
			));
			
			
			$objValue = \ContentValueModel::findByCidandPidandRid($this->cid, $this->id, $this->rid);
			
			if ($objValue !== null && $objValue->selectField)
			{
				$subOption = $objValue->selectField;
			}
			
			// Set the to the first by default
			if (!$subOption)
			{
				$subOption = array_keys($arrOptions)[0];
			}
			
			// add the pattern to palettes
			$colSubPattern = \ContentPatternModel::findPublishedByPidAndTableAndSubOption($this->id, 'tl_content_subpattern', substr($subOption, 1), array('order'=>'sorting ASC'));

			if ($colSubPattern === null)
			{
				return;
			}

			foreach($colSubPattern as $objSubPattern)
			{
				// construct dca for pattern
				$strClass = \Agoat\ContentBlocks\Pattern::findClass($objSubPattern->type);
					
				if (!class_exists($strClass))
				{
					\System::log('Pattern element class "'.$strClass.'" (pattern element "'.$objSubPattern->type.'") does not exist', __METHOD__, TL_ERROR);
				}
				else
				{
					$objSubPatternClass = new $strClass($objSubPattern);
					$objSubPatternClass->cid = $this->cid;
					$objSubPatternClass->rid = $this->rid * 100;
					$objSubPatternClass->alias = $this->alias;			

					$objSubPatternClass->construct();
				}
			}

			
		}
		else
		{
			// generate DCA
			$this->generateDCA('checkBox', array
			(
				'inputType' 	=>	'checkbox',
				'label'			=>	array($this->label, $this->description),
				'eval'			=>	array
				(
					'tl_class'			=>	'w50 clr m12',
					'onclick'			=>	'Backend.autoSubmit(\'tl_content\')',
				)
			));
			
			$objValue = \ContentValueModel::findByCidandPidandRid($this->cid, $this->id, $this->rid);
			
			if ($objValue !== null && $objValue->checkBox)
			{
				// add the pattern to palettes
				$colSubPattern = \ContentPatternModel::findPublishedByPidAndTable($this->id, 'tl_content_subpattern', array('order'=>'sorting ASC'));
				
				if ($colSubPattern === null)
				{
					return;
				}

				foreach($colSubPattern as $objSubPattern)
				{
					// construct dca for pattern
					$strClass = \Agoat\ContentBlocks\Pattern::findClass($objSubPattern->type);
						
					if (!class_exists($strClass))
					{
						\System::log('Pattern element class "'.$strClass.'" (pattern element "'.$objSubPattern->type.'") does not exist', __METHOD__, TL_ERROR);
					}
					else
					{
						$objSubPatternClass = new $strClass($objSubPattern);
						$objSubPatternClass->cid = $this->cid;
						$objSubPatternClass->rid = $this->rid * 100;
						$objSubPatternClass->alias = $this->alias;			

						$objSubPatternClass->construct();
					}
				}
			}
		}

	}


	/**
	 * prepare a field view for the backend
	 *
	 * @param array $arrAttributes An optional attributes array
	 */
	public function view()
	{
		return '<div class="tl_checkbox_single_container"><input class="tl_checkbox" value="1" type="checkbox"> <label>' . $this->label . '</label><p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';	
	}


	/**
	 * prepare the values for the frontend template
	 *
	 * @param array $arrAttributes An optional attributes array
	 */	
	public function compile()
	{
		if ($this->subPatternType == 'options')
		{
			if ($this->Value->selectField)
			{
				// add new alias to the value mapper
				$this->arrMapper[] = $this->alias;
				
				// write the option to the template
				$alias = $this->alias;
				$this->alias = 'option';
				$this->writeToTemplate(substr($this->Value->selectField,1));
				$this->alias = $alias;


				// get the pattern model collection
				$colSubPattern = \ContentPatternModel::findPublishedByPidAndTableAndSubOption($this->id, 'tl_content_subpattern', substr($this->Value->selectField, 1));

				if ($colSubPattern === null)
				{
					return;
				}
				
				// prepare values for every pattern
				foreach($colSubPattern as $objSubPattern)
				{
					// don´t show the invisible or system pattern
					if (in_array($objSubPattern->type, $GLOBALS['TL_SYS_PATTERN']))
					{
						continue;
					}

			
					$strClass = Pattern::findClass($objSubPattern->type);
						
					if (!class_exists($strClass))
					{
						System::log('Pattern element class "'.$strClass.'" (pattern element "'.$objSubPattern->type.'") does not exist', __METHOD__, TL_ERROR);
					}
					else
					{
						$objSubPatternClass = new $strClass($objSubPattern);
						$objSubPatternClass->cid = $this->cid;
						$objSubPatternClass->rid = $this->rid * 100;
						$objSubPatternClass->Template = $this->Template;
						$objSubPatternClass->arrMapper = $this->arrMapper;
						$objSubPatternClass->arrValues = $this->arrValues;
						$objSubPatternClass->Value = $this->arrValues[$objSubPattern->id][$this->rid * 100];
						
						$objSubPatternClass->compile();
					}
				}
			}
			
		}
		else
		{
			if ($this->Value->checkBox)
			{
				// add new alias to the value mapper
				$this->arrMapper[] = $this->alias;

				// get the pattern model collection
				$colSubPattern = \ContentPatternModel::findPublishedByPidAndTable($this->id, 'tl_content_subpattern');

				if ($colSubPattern === null)
				{
					return;
				}
				
				// prepare values for every pattern
				foreach($colSubPattern as $objSubPattern)
				{
					// don´t show the invisible or system pattern
					if (in_array($objSubPattern->type, $GLOBALS['TL_SYS_PATTERN']))
					{
						continue;
					}

			
					$strClass = Pattern::findClass($objSubPattern->type);
						
					if (!class_exists($strClass))
					{
						System::log('Pattern element class "'.$strClass.'" (pattern element "'.$objSubPattern->type.'") does not exist', __METHOD__, TL_ERROR);
					}
					else
					{
						$objSubPatternClass = new $strClass($objSubPattern);
						$objSubPatternClass->cid = $this->cid;
						$objSubPatternClass->rid = $this->rid * 100;
						$objSubPatternClass->Template = $this->Template;
						$objSubPatternClass->arrMapper = $this->arrMapper;
						$objSubPatternClass->arrValues = $this->arrValues;
						$objSubPatternClass->Value = $this->arrValues[$objSubPattern->id][$this->rid * 100];
						
						$objSubPatternClass->compile();
					}
				}
			}
		}
	}
}
