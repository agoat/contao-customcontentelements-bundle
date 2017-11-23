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

use Contao\StringUtil;


class PatternSubPattern extends Pattern
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

		// Execute Ajax action
		if (\Environment::get('isAjaxRequest') && \Input::post('pattern') == $this->pattern . (($this->parent > 0) ? '_' . $this->parent : ''))
		{
			switch (\Input::post('action'))
			{
				case 'toggleSubpattern':
					dump('toggling');
					$objGroup = \DataModel::findById(\Input::post('id'));
					
					if ($objGroup !== null)
					{
						$objGroup->checkBox = \Input::post('state');
						
						$objGroup->save();
					dump($objGroup);
						
						$this->data = $objGroup;
					}
					break;
				
				case 'switchSubpattern':
					dump('switching');
					$objGroup = \DataModel::findById(\Input::post('id'));
					
					if ($objGroup !== null)
					{
						$objGroup->singleSelectField = \Input::post('option');
						
						$objGroup->save();
					dump($objGroup);
						
						$this->data = $objGroup;
					}
					break;
			}
		}
		
		if ($this->subPatternType == 'options')
		{
			$strGroup = 'default';
			foreach (StringUtil::deserialize($this->options) as $arrOption)
			{
				if ($arrOption['group'])
				{
					$strGroup = $arrOption['label'];
					continue;
				}	
				
				if ($arrOption['default'] || !$default)
				{
					$default = 'v'.$arrOption['value'];
				}
				
				$arrOptions[$strGroup]['v'.$arrOption['value']] = $arrOption['label'];
			}
			
			if (!$default)
			{
				$default = 'v'.$arrOptions[0]['value'];
			}
			
			// No groups 
			if (count($arrOptions) < 2 )
			{
				$arrOptions = array_values($arrOptions)[0];
			}
		
			// Generate DCA
			$this->generateDCA('singleSelectField', array
			(
				'inputType' 	=>	'select',
				'label'			=>	array($this->label, $this->description),
				'default'		=>	$default,
				'options'		=>	$arrOptions,
				'eval'			=>	array
				(
					'tl_class'		=>	'w50 clr',
					'onchange'		=>	'AjaxRequest.switchSubpattern(this, \'' . $this->pattern . (($this->parent > 0) ? '_' . $this->parent : '') . '\', ' . $this->data->id . ')'
				)
			));
			
			$option = $this->data->singleSelectField;
	
			if (!$option)
			{
				$option = $default;
			}

			$colSubPattern = \PatternModel::findVisibleByPidAndTableAndOption($this->id, 'tl_subpattern', substr($option, 1));	
		}
		else
		{
			// Generate DCA
			$this->generateDCA('checkBox', array
			(
				'inputType' 	=>	'checkbox',
				'label'			=>	array($this->label, $this->description),
				'eval'			=>	array
				(
					'tl_class'		=>	'w50 clr m12',
					'onclick'		=>	'AjaxRequest.toggleSubpattern(this, \'' . $this->pattern . (($this->parent > 0) ? '_' . $this->parent : '') . '\', ' . $this->data->id . ')'
				)
			));

			if ($this->data->checkBox)
			{
				$colSubPattern = \PatternModel::findVisibleByPidAndTable($this->id, 'tl_subpattern');
			}
		}

		// Add subpattern
		if ($colSubPattern === null)
		{
			return;
		}

		$GLOBALS['TL_DCA']['tl_content']['palettes'][$this->element] .= ',[' . $this->pattern . (($this->parent > 0) ? '_' . $this->parent : '') . ']';
		
		$arrData = array();
		
		$colData = \DataModel::findByPidAndParent($this->pid, $this->data->id);
	
		if ($colData !== null)
		{
			foreach ($colData as $objData)
			{
				$arrData[$objData->pattern] = $objData;
			}							
		}

		foreach($colSubPattern as $objSubPattern)
		{
			// Construct dca for pattern
			$strClass = Pattern::findClass($objSubPattern->type);
			$bolData = Pattern::hasData($objSubPattern->type);
				
			if (!class_exists($strClass))
			{
				\System::log('Pattern element class "'.$strClass.'" (pattern element "'.$objSubPattern->type.'") does not exist', __METHOD__, TL_ERROR);
			}
			else
			{
				if ($bolData && !isset($arrData[$objSubPattern->alias]))
				{
					$arrData[$objSubPattern->alias] = new \DataModel();
					$arrData[$objSubPattern->alias]->pid = $this->pid;
					$arrData[$objSubPattern->alias]->pattern = $objSubPattern->alias;
					$arrData[$objSubPattern->alias]->parent = $this->data->id;
			
					$arrData[$objSubPattern->alias]->save();
				}

				$objSubPatternClass = new $strClass($objSubPattern);
				$objSubPatternClass->pid = $this->pid;
				$objSubPatternClass->pattern = $objSubPattern->alias;
				$objSubPatternClass->parent = $this->data->id;
				$objSubPatternClass->element = $this->element;	
				$objSubPatternClass->data = $arrData[$objSubPattern->alias];							

				$objSubPatternClass->construct();
			}
		}
	
		$GLOBALS['TL_DCA']['tl_content']['palettes'][$this->element] .= ',[EOF]';
	}

		
	/**
	 * prepare a field view for the backend
	 *
	 * @param array $arrAttributes An optional attributes array
	 */
	public function view()
	{
		if ($this->subPatternType == 'options')
		{
			$strPreview = '<div class="w50 clr">';
			$strPreview .= '<h3 style="margin: 0;"><label>' . $this->label . '</label></h3>';
			$strPreview .= '<select class="tl_select" style="width: 412px;" onchange="$$(\'.' . $this->alias . '\').hide();$(this.options[this.selectedIndex].value).show();">';

			foreach (StringUtil::deserialize($this->options) as $arrOption)
			{
				if ($arrOption['group'])
				{
					if ($blnOpenGroup)
					{
						$strPreview .= '</optgroup>';
					}
					
					$strPreview .= '<optgroup label="&nbsp;' . StringUtil::specialchars($arrOption['label']) . '">';
					$blnOpenGroup = true;
					continue;
				}
						
				if ($arrOption['default'] || !$default)
				{
					$default = $arrOption['value'];
				}	

				$strPreview .= '<option value="' . StringUtil::specialchars($arrOption['value']) . '"' . (($arrOption['default']) ? ' selected' : '') . '>' . StringUtil::specialchars($arrOption['label']) . '</option>';
			}

			if ($blnOpenGroup)
			{
				$strPreview .= '</optgroup>';
			}

			$strPreview .= '</select><p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';

			// add the sub pattern
			foreach (StringUtil::deserialize($this->options) as $arrOption)
			{
				if (!$arrOption['group'])
				{
					$strPreview .=  '<div id="' .  $arrOption['value'] . '" class="sub_pattern ' . $this->alias . '" style="display: ' . (($arrOption['value'] == $default) ? 'block' : 'none'). ';">';	
						
					$colSubPattern = \PatternModel::findVisibleByPidAndTableAndOption($this->id, 'tl_subpattern', $arrOption['value']);
					
					if ($colSubPattern === null)
					{
						continue;
					}

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

							$strPreview .= $objSubPatternClass->view();
						}
					}
					
					$strPreview .=  '</div>';	
				}
			}

		}
		else
		{
			$strPreview =  '<div class="w50 clr">';	
			$strPreview .=  '<div class="tl_checkbox_single_container"><input class="tl_checkbox" type="checkbox" onclick="$(\'sub_' . $this->id . '\').toggle();"> <label onclick="$(\'sub_' . $this->id . '\').toggle();">' . $this->label . '</label><p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';	
			$strPreview .=  '<div id="sub_' . $this->id . '" class="sub_pattern" style="display: none;">';	

			// add the sub pattern
			$colSubPattern = \PatternModel::findVisibleByPidAndTable($this->id, 'tl_subpattern');
			
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
		
						$strPreview .= $objSubPatternClass->view();
					}
				}
			}

			$strPreview .=  '</div></div>';	
		}

		return $strPreview;
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
			if ($this->data->singleSelectField)
			{

			// Add select option value (instead of the alias) to the value mapper
				$this->arrMapper[] = substr($this->data->singleSelectField,1);

				$colSubPattern = \PatternModel::findVisibleByPidAndTableAndOption($this->id, 'tl_subpattern', substr($this->data->singleSelectField, 1));
			}
		}
		else
		{
			if ($this->data->checkBox)
			{
				// Add new alias to the value mapper
				$this->arrMapper[] = $this->alias;
		
				$colSubPattern = \PatternModel::findVisibleByPidAndTable($this->id, 'tl_subpattern');
			}
		}

		if ($colSubPattern === null)
		{
			return;
		}

		$colData = \DataModel::findByPidAndParent($this->pid, $this->data->id);
	
		if ($colData !== null)
		{
			foreach ($colData as $objData)
			{
				$arrData[$objData->pattern] = $objData;
			}							
		}
		
		// prepare values for every pattern
		foreach($colSubPattern as $objSubPattern)
		{
			if (!Pattern::hasOutput($objSubPattern->type))
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
				$objSubPatternClass->pid = $this->pid;
				$objSubPatternClass->Template = $this->Template;
				$objSubPatternClass->arrMapper = $this->arrMapper;
				$objSubPatternClass->data = $arrData[$objSubPattern->alias];							
						
				$objSubPatternClass->compile();
			}
		}
		
		
	}
}