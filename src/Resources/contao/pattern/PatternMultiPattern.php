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
		$objGroupCount = \ContentValueModel::findByCidandPidandRid($this->cid, $this->id, $this->rid);
		
		if ($objGroupCount !== null)
		{
			$intGroupCount = $objGroupCount->count;
			
		}
	
		// Set min of 1
		if ($intGroupCount < 1)
		{
			$intGroupCount = 1;
		}
		
		// add multi group widget
		$this->generateDCA('count', array
		(
			'inputType' =>	'multigroup',
			'eval'		=>	array
			(
				'groupMax'			=>	$this->multiPatternMax, 
				'groupCount'		=>	$intGroupCount, 
				'pid'				=>	$this->id, 
				'rid'				=>	$this->rid, 
				'strCommand'		=>	'cmd_multigroup-' . $this->id . '-' . $this->rid, 
			)
		));

		
		$prid = $this->rid;
		
		for ($rid=0; $rid < $intGroupCount; $rid++)
		{
			
			$this->rid = ($prid * 100) + $rid;

			// add multigroupstart widget (with add, delete and move buttons)
			$this->generateDCA('multigroupstart', array
			(
				'inputType' =>	'multigroupstart',
				'eval'		=>	array
				(
					'title'			=>	$this->label, 
					'desc'			=>	$this->description, 
					'up'			=>	($rid != 0), 
					'down'			=>	($rid != $intGroupCount-1), 
					'delete'		=>	($intGroupCount > 1), 
					'insert'		=>	($intGroupCount < $this->multiPatternMax), 
					'cid'			=>	$this->rid, 
					'strCommand'	=>	'cmd_multigroup-' . $this->id . '-' . $prid, 
				)
			), false);
				
			
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
					$objMultiPatternClass = new $strClass($objMultiPattern);
					$objMultiPatternClass->cid = $this->cid;
					$objMultiPatternClass->rid = $this->rid;
					$objMultiPatternClass->alias = $this->alias;			
					
					$objMultiPatternClass->construct();
				}				
			}
			
		
			// add multigroupstopwidget
			$this->generateDCA('multigroupstop', array
			(
				'inputType' =>	'multigroupstop',
				'eval'		=>	array
				(
					'pid'				=>	$this->id, 
					'rid'				=>	$this->rid, 
				)
			), false);
		
	
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
		// add new alias to the value mapper
		$this->arrMapper[] = $this->alias;

		// get the pattern model collection
		$colPattern = \ContentPatternModel::findPublishedByPidAndTable($this->id, 'tl_content_subpattern');

		if ($colPattern === null)
		{
			return;
		}

		// get count from tl_content_value
		$objGroupCount = \ContentValueModel::findByCidandPidandRid($this->cid, $this->id, $this->rid);
		
		if ($objGroupCount === null)
		{
			return;			
		}
		
		for ($rid=0; $rid < $objGroupCount->count; $rid++)
		{
		
			// prepare values for every pattern
			foreach($colPattern as $objPattern)
			{
				// exclude system pattern
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
					$objMultiPatternClass = new $strClass($objPattern);
					$objMultiPatternClass->cid = $this->cid;
					$objMultiPatternClass->rid = ($this->rid * 100) + $rid;
					$objMultiPatternClass->Template = $this->Template;
					$objMultiPatternClass->arrMapper = array_merge($this->arrMapper, array($rid));
					$objMultiPatternClass->arrValues = $this->arrValues;
					$objMultiPatternClass->Value = $this->arrValues[$objPattern->id][($this->rid * 100) + $rid];
					
					$objMultiPatternClass->compile();
				
				}
			}
		}
			
	}
}
