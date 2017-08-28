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

namespace Agoat\ContentElements;


class PatternMultiPattern extends Pattern
{
	
	/**
	 * generate the DCA construct
	 */
	public function construct()
	{
		if (!isset($this->parent))
		{
			$this->parent = 0;
		}

		// Execute Ajax actions
		if (\Environment::get('isAjaxRequest') && \Input::post('pattern') == $this->pattern . (($this->parent > 0) ? '_' . $this->parent : ''))
		{
			switch (\Input::post('action'))
			{
				case 'insertGroup':
					if ($this->numberOfGroups > \DataModel::findByPidAndPatternAndParent($this->pid, $this->pattern, $this->parent)->count())
					{
						$newGroup = new \DataModel();

						$newGroup->pid = $this->pid;
						$newGroup->parent = $this->parent;
						$newGroup->pattern = $this->pattern;
						$newGroup->sorting = $this->getNewPosition((strlen(\Input::post('pid'))) ? \Input::post('pid') : 0);
						$newGroup->tstamp = time();
						
						$newGroup->save();
						
						$newGroupId = $newGroup->id;
					}
					break;
					
				case  'moveGroup':
					$objGroup = \DataModel::findById(\Input::post('id'));
					
					if ($objGroup !== null)
					{
						$objGroup->sorting = $this->getNewPosition((strlen(\Input::post('pid'))) ? \Input::post('pid') : 0);
						
						$objGroup->save();
					}
					break;
					
				case  'deleteGroup':
					if (1 < \DataModel::findByPidAndPatternAndParent($this->pid, $this->pattern, $this->parent)->count())
					{
						$objGroup = \DataModel::findById(\Input::post('id'));
						
						if ($objGroup !== null)
						{
							$objGroup->delete();
						}
						
						$this->reviseDataTable();
					}
					break;
			}
			
			$bolAjax = true;
		}
		
		$colGroupData = \DataModel::findByPidAndPatternAndParent($this->pid, $this->pattern, $this->parent, array('order'=>'sorting ASC'));

		if ($colGroupData->count() == 1 && $colGroupData->tstamp == 0 && $colGroupData->sorting == 0)
		{
			$colGroupData->tstamp = time();
			$colGroupData->sorting = 128;
			
			$colGroupData->save();
		}

		// Add insert group button
		$this->generateDCA('group', array
		(
			'inputType' =>	'group',
			'eval'		=>	array
			(
				'insert'	=>	(count($colGroupData) < $this->numberOfGroups) ? true : false, 
				'pattern'	=>	$this->pattern . (($this->parent > 0) ? '_' . $this->parent : ''),
				'max'		=>	$this->numberOfGroups,
				'tl_class'	=>	'clr'
			)
		), true, false);
		
		$GLOBALS['TL_DCA']['tl_content']['palettes'][$this->element] .= ',[' . $this->pattern . (($this->parent > 0) ? '_' . $this->parent : '') . ']';
		
		foreach ($colGroupData as $objGroupData)
		{
			if (\Environment::get('isAjaxRequest') && $bolAjax && $newGroupId != $objGroupData->id)
			{
				continue;
			}
			
			$this->data = $objGroupData;

			// Add group widget (with add, delete and move buttons)
			$this->generateDCA('sorting', array
			(
				'inputType' =>	'groupstart',
				'eval'		=>	array
				(
					'title'		=>	$this->label, 
					'desc'		=>	$this->description, 
					'gid'		=>	$this->data->id,
					'insert'	=>	(count($colGroupData) < $this->numberOfGroups) ? true : false, 
					'delete'	=>	(count($colGroupData) > 1) ? true : false,
					'move'		=>	(count($colGroupData) > 1) ? true : false,
					'max'		=>	$this->numberOfGroups,
					'pattern'	=>	$this->pattern . (($this->parent > 0) ? '_' . $this->parent : ''),
					'tl_class'	=>	'group_' . $this->data->id
				),
			));
				
			$arrData = array();

			$colData = \DataModel::findByPidAndParent($this->pid, $objGroupData->id);

			if ($colData !== null)
			{
				foreach ($colData as $objData)
				{
					$arrData[$objData->pattern] = $objData;
				}							
			}
	
			$colMultiPattern = \PatternModel::findVisibleByPidAndTable($this->id, 'tl_subpattern');
			
			// Add sub pattern
			if ($colMultiPattern !== null)
			{
				foreach($colMultiPattern as $objMultiPattern)
				{
					// Construct dca for pattern
					$strClass = Pattern::findClass($objMultiPattern->type);
					$bolData = Pattern::hasData($objMultiPattern->type);
						
					if (!class_exists($strClass))
					{
						\System::log('Pattern element class "'.$strClass.'" (pattern element "'.$objMultiPattern->type.'") does not exist', __METHOD__, TL_ERROR);
					}
					else
					{
						if ($bolData && !isset($arrData[$objMultiPattern->alias]))
						{
							$arrData[$objMultiPattern->alias] = new \DataModel();
							$arrData[$objMultiPattern->alias]->pid = $this->pid;
							$arrData[$objMultiPattern->alias]->pattern = $objMultiPattern->alias;
							$arrData[$objMultiPattern->alias]->parent = $objGroupData->id;
					
							$arrData[$objMultiPattern->alias]->save();
						}
						
						$objMultiPatternClass = new $strClass($objMultiPattern);
						$objMultiPatternClass->pid = $this->pid;
						$objMultiPatternClass->pattern = $objMultiPattern->alias;
						$objMultiPatternClass->parent = $objGroupData->id;
						$objMultiPatternClass->element = $this->element;
						$objMultiPatternClass->data = $arrData[$objMultiPattern->alias];							
								
						$objMultiPatternClass->construct();
					}				
				}
			}
		
			// Close group widget
			$this->generateDCA('eog', array
			(
				'inputType' =>	'groupstop',
				'eval'		=>	array
				(
					'tl_class'	=>	'clr'	
				)
			), true, false);
		}

		$GLOBALS['TL_DCA']['tl_content']['palettes'][$this->element] .= ',[EOF]';

		// Add group script widget
		$this->generateDCA('script', array
		(
			'inputType' =>	'groupscript',
			'eval'		=>	array
			(
				'pattern'	=>	$this->pattern . (($this->parent > 0) ? '_' . $this->parent : '')
			)
		), true, false);

	}


	/**
	 * prepare a field view for the backend
	 *
	 * @param array $arrAttributes An optional attributes array
	 */
	public function view()
	{
		$strPreview = '<div class="tl_group_header clr"><div class="tl_content_right  click2edit">';
		$strPreview .= '<button type="button" class="insert-handle" title="' . $GLOBALS['TL_LANG']['MSC']['group']['new']['top'] . '">' . \Image::getHtml('new.svg', 'new') . ' ' . $GLOBALS['TL_LANG']['MSC']['group']['new']['label'] . '</button>';
		$strPreview .= '</div></div>';

		$strGroupPreview = '<div class="tl_group clr">';
		$strGroupPreview .= '<div class="tl_content_right  click2edit">';

		$strGroupPreview .= ' <button type="button" class="delete-handle">' . \Image::getHtml('delete.svg', 'delete', 'title="' . $GLOBALS['TL_LANG']['MSC']['group']['delete'] . '"') . '</button>';
		$strGroupPreview .= ' <button type="button" class="insert-handle">' . \Image::getHtml('new.svg', 'new', 'title="' . $GLOBALS['TL_LANG']['MSC']['group']['new']['after'] . '"') . '</button>';
		$strGroupPreview .= ' <button type="button" class="drag-handle">' . \Image::getHtml('drag.svg', 'drag', 'title="' . $GLOBALS['TL_LANG']['MSC']['group']['drag'] . '"') . '</button>';

		$strGroupPreview .= '</div>';
		$strGroupPreview .= '<h3><label>' . $this->label . '</label></h3>';
		$strGroupPreview .= '<p class="tl_help tl_tip" title="">' . $this->description . '</p>';	
		$strGroupPreview .= '<div id="sub_group" class="subpal cf">';
		
		// add the sub pattern
		$colMultiPattern = \PatternModel::findVisibleByPidAndTable($this->id, 'tl_subpattern');
		
		if ($colMultiPattern !== null)
		{
			foreach($colMultiPattern as $objMultiPattern)
			{
				// construct dca for pattern
				$strClass = Pattern::findClass($objMultiPattern->type);
					
				if (!class_exists($strClass))
				{
					\System::log('Pattern element class "'.$strClass.'" (pattern element "'.$objMultiPattern->type.'") does not exist', __METHOD__, TL_ERROR);
				}
				else
				{
					$objSubPatternClass = new $strClass($objMultiPattern);
	
					$strGroupPreview .= $objSubPatternClass->view();
				}
			}
		}

		$strGroupPreview .=  '</div></div>';

		// Add group twice
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
		// Add new alias to the value mapper
		$this->arrMapper[] = $this->alias;

		$colGroupData = \DataModel::findByPidAndPatternAndParent($this->pid, $this->alias, $this->data->parent, array('order'=>'sorting ASC'));
	
		if ($colGroupData === null)
		{
			return;
		}
		
		$colMultiPattern = \PatternModel::findVisibleByPidAndTable($this->id, 'tl_subpattern');

		if ($colMultiPattern === null)
		{
			return;
		}
			
		$count = 0;
		
		foreach ($colGroupData as $objGroupData)
		{
			$arrData = array();

			$colData = \DataModel::findByPidAndParent($this->pid, $objGroupData->id);

			if ($colData !== null)
			{
				foreach ($colData as $objData)
				{
					$arrData[$objData->pattern] = $objData;
				}							
			}

			foreach($colMultiPattern as $objMultiPattern)
			{
				if (!Pattern::hasOutput($objMultiPattern->type))
				{
					continue;
				}	
		
				$strClass = Pattern::findClass($objMultiPattern->type);
					
				if (!class_exists($strClass))
				{
					System::log('Pattern element class "'.$strClass.'" (pattern element "'.$objMultiPattern->type.'") does not exist', __METHOD__, TL_ERROR);
				}
				else
				{
					$objSubPatternClass = new $strClass($objMultiPattern);
					$objSubPatternClass->pid = $this->pid;
					$objSubPatternClass->Template = $this->Template;
					$objSubPatternClass->arrMapper = array_merge($this->arrMapper, array($count));
					$objSubPatternClass->data = $arrData[$objMultiPattern->alias];							
							
					$objSubPatternClass->compile();
				}
			}
			
			$count++;
		}
	}
	

	protected function getNewPosition($gid=0)
	{
		$db = \Database::getInstance();
		
		// Insert after group
		if ($gid > 0)
		{
			$objSorting = $db->prepare("SELECT pid, sorting FROM tl_data WHERE id=?")
							 ->limit(1)
							 ->execute($gid);
							 
			if ($objSorting->numRows)
			{
				$curSorting = $objSorting->sorting;
				
				$objNextSorting = $db->prepare("SELECT MIN(sorting) AS sorting FROM tl_data WHERE parent=? AND pattern=? AND sorting>?")
									 ->execute($this->parent, $this->pattern, $curSorting);

				 // Select sorting value of the next record
				if ($objNextSorting->sorting !== null)
				{
					$nxtSorting = $objNextSorting->sorting;
					
					// Resort if the new sorting value is no integer or bigger than a MySQL integer
					if ((($curSorting + $nxtSorting) % 2) != 0 || $nxtSorting >= 4294967295)
					{
						$count = 1;
						
						$objNewSorting = $db->prepare("SELECT id, sorting FROM tl_data WHERE parent=? AND pattern=? ORDER BY sorting")
											->execute($this->parent, $this->pattern);
														
						while ($objNewSorting->next())
						{
							$db->prepare("UPDATE tl_data SET sorting=? WHERE id=?")
							   ->limit(1)
							   ->execute(($count++ * 128), $objNewSorting->id);

							if ($objNewSorting->sorting == $curSorting)
							{
								$newSorting = ($count++ * 128);
							}
						}
					}
					else $newSorting = (($curSorting + $nxtSorting) / 2);
				}
				else $newSorting = ($curSorting + 128);
			}
			else $newSorting = 128;
		}
		// Insert on first postion
		else
		{
			$objSorting = $db->prepare("SELECT MIN(sorting) AS sorting FROM tl_data WHERE parent=? AND pattern=?")
							 ->execute($this->parent, $this->pattern);
												 
			if ($objSorting->numRows)
			{
				$curSorting = $objSorting->sorting;
				
				// Resort if the new sorting value is not an integer or smaller than 1
				if (($curSorting % 2) != 0 || $curSorting < 1)
				{
					$count = 2;

					$objNewSorting = $db->prepare("SELECT id FROM tl_data WHERE parent=? AND pattern=? ORDER BY sorting")
										->execute($this->parent, $this->pattern);
				
					while ($objNewSorting->next())
					{
						$db->prepare("UPDATE tl_data SET sorting=? WHERE id=?")
						   ->limit(1)
						   ->execute(($count++ * 128), $objNewSorting->id);
					}
					
					$newSorting = 128;
				}
				else $newSorting = ($curSorting / 2);
			}
			else $newSorting = 128;
		}
		
		return $newSorting;
	}

	
	protected function reviseDataTable()
	{
		$db = \Database::getInstance();
		
		$objData = $db->execute("SELECT id FROM tl_data WHERE parent > 0 AND parent NOT IN (SELECT id FROM tl_data)");
	
		while ($objData->numRows > 0)
		{
			$objStmt = $db->execute("DELETE FROM tl_data WHERE id IN (" . implode(',', $objData->fetchEach('id')) . ")");
			$objData = $db->execute("SELECT id FROM tl_data WHERE parent > 0 AND parent NOT IN (SELECT id FROM tl_data)");
		}
	}
}