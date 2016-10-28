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

		if ($this->subpattern == 'options')
		{
			// Generate options
			$strGroup = 'default';
			foreach (StringUtil::deserialize($this->options) as $arrOption)
			{
				if ($arrOption['group'])
				{
					$strGroup = $arrOption['label'];
					continue;
				}	
				
				if ($arrOption['default'])
				{
					$default = $arrOption['value'];
				}	
				
				$arrOptions[$strGroup]['v'.$arrOption['value']] = $arrOption['label'];
			}
			
			// no groups 
			if (count($arrOptions) < 2)
			{
				$arrOptions = $arrOptions['default'];
			}


			$this->generateDCA('selectField', array
			(
				'inputType' 	=>	'select',
				'label'			=>	array($this->label, $this->description),
				'default'		=>	$default,
				'options'		=>	$arrOptions,
				'eval'			=>	array
				(
					'submitOnChange'	=>	true
				),
			));
			
			// add the pattern to palettes
			$colSubPattern = \ContentPatternModel::findPublishedByPidAndTable($this->id, 'tl_content_subpattern', array('order'=>'sorting ASC'));
			
			if ($colSubPattern === null)
			{
				return;
			}


			while($colSubPattern->next())
			{

				// don´t load values for system pattern (because they have no ?? maybe a replica counter ??)
				if (!in_array($colSubPattern->current()->type, array('section', 'explanation', 'visibility', 'protection')))
				{
					$colValue = \ContentValueModel::findByCidandPid($objContent->id, $colSubPattern->current()->id);
				
					if ($colValue !== null)
					{
						foreach ($colValue as $objValue)
						{
							$this->arrLoadedValues[$objValue->rid][$colSubPattern->current()->id] = $objValue->row();
						}							
					}

				}
			
				// construct dca for pattern
				$strClass = \Agoat\ContentBlocks\Pattern::findClass($colSubPattern->current()->type);
					
				if (!class_exists($strClass))
				{
					\System::log('Pattern element class "'.$strClass.'" (pattern element "'.$colSubPattern->current()->type.'") does not exist', __METHOD__, TL_ERROR);
				}
				else
				{
					$objPatternClass = new $strClass($colSubPattern->current());
					$objPatternClass->cid = $objContent->id;
				//	$objPatternClass->subpalette = true;
					$objPatternClass->alias = $this->virtualFieldAlias . '_aa';			
					
					$objPatternClass->construct();
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
			
			if ($objValue->checkBox)
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
						$objPatternClass = new $strClass($objSubPattern);
						$objPatternClass->cid = $this->cid;
						$objPatternClass->rid = $this->rid;
						$objPatternClass->alias = $this->alias;			

						$objPatternClass->construct();
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
		if ($this->subType == 'button' && $this->Value->checkBox)
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
					$objPatternClass->rid = $this->rid;
					$objPatternClass->Template = $this->Template;
					$objPatternClass->arrMapper = $this->arrMapper;
					$objPatternClass->arrValues = $this->arrValues;
					$objPatternClass->Value = $this->arrValues[$objPattern->id][$this->rid];

					$objPatternClass->compile();
				}
			}
		}
	}
}
