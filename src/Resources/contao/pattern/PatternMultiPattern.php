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
				'numberOfGroups'	=>	$this->numberOfGroups, 
				'groupCount'		=>	$intGroupCount, 
				'pid'				=>	$this->id, 
				'rid'				=>	$this->rid, 
				'strCommand'		=>	'cmd_multigroup-' . $this->id . '-' . $this->rid, 
				'tl_class'			=>	'clr'	
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
					'insert'		=>	($intGroupCount < $this->numberOfGroups), 
					'rid'			=>	$this->rid,
					'strCommand'	=>	'cmd_multigroup-' . $this->id . '-' . $prid, 
					'tl_class'		=>	'clr'	
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
					'tl_class'		=>	'clr'	
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
		$strPreview = '<div class="tl_multigroup_header clr">';
		$strPreview .= '<a href="javascript:void(0);" title="' . $GLOBALS['TL_LANG']['MSC']['mg_new']['top'] . '">' . \Image::getHtml('new.svg', 'new') . ' ' . $GLOBALS['TL_LANG']['MSC']['mg_new']['label'] . '</a>';
		$strPreview .= '</div>';

		$strGroupPreview = '<div class="tl_multigroup clr">';
		$strGroupPreview .= '<div class="tl_multigroup_right click2edit">';

		$strGroupPreview .= '<a href="javascript:void(0);">' . \Image::getHtml('up.svg', 'up', 'title="' . $GLOBALS['TL_LANG']['MSC']['mg_up'] . '"') . '</a>';
		$strGroupPreview .= ' <a href="javascript:void(0);">' . \Image::getHtml('down.svg', 'down', 'title="' . $GLOBALS['TL_LANG']['MSC']['mg_down'] . '"') . '</a>';
		$strGroupPreview .= ' <a href="javascript:void(0);">' . \Image::getHtml('delete.svg', 'delete', 'title="' . $GLOBALS['TL_LANG']['MSC']['mg_delete'] . '"') . '</a>';
		$strGroupPreview .= ' <a href="javascript:void(0);">' . \Image::getHtml('new.svg', 'new', 'title="' . $GLOBALS['TL_LANG']['MSC']['mg_new']['after'] . '"') . '</a>';

		$strGroupPreview .= '</div>';
		$strGroupPreview .= '<h3><label>' . $this->label . '</label></h3>';
		$strGroupPreview .= '<p class="tl_help tl_tip" title="">' . $this->description . '</p>';	
		$strGroupPreview .= '<div class="tl_multigroup_box">';
		
		// add the sub pattern
		$colSubPattern = \ContentPatternModel::findPublishedByPidAndTable($this->id, 'tl_content_subpattern', array('order'=>'sorting ASC'));
		
		if ($colSubPattern !== null)
		{
			foreach($colSubPattern as $objSubPattern)
			{
				// construct dca for pattern
				$strClass = Pattern::findClass($objSubPattern->type);
					
				if (!class_exists($strClass))
				{
					\System::log('Pattern element class "'.$strClass.'" (pattern element "'.$objSubPattern->type.'") does not exist', __METHOD__, TL_ERROR);
				}
				else
				{
					$objSubPatternClass = new $strClass($objSubPattern);
	
					$strGroupPreview .= $objSubPatternClass->view();
				}
			}
		}

		$strGroupPreview .=  '<div class="clr widget"></div></div></div>';

		// add the sub pattern twice
		$strPreview .=  $strGroupPreview;	
		$strPreview .=  $strGroupPreview;	

			

		return $strPreview;

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
