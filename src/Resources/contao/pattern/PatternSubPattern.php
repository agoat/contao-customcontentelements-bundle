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
					$default = 'v'.$arrOption['value'];
				}	
				
				$arrOptions[$strGroup]['v'.$arrOption['value']] = $arrOption['label'];
			}
		
			
			// no groups 
			if (count($arrOptions) < 2 )
			{
				$arrOptions = array_values($arrOptions)[0];
			}
				
		
			// generate DCA
			$this->generateDCA('selectField', array
			(
				'inputType' 	=>	'select',
				'label'			=>	array($this->label, $this->description),
				'default'		=>	$default,
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
			
			if (!$subOption)
			{
				$subOption = $default;
			}

			// add the pattern to palettes
			$colSubPattern = \ContentPatternModel::findPublishedByPidAndTableAndSubOption($this->id, 'tl_content_subpattern', substr($subOption,1), array('order'=>'sorting ASC'));

			if ($colSubPattern === null)
			{
				return;
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
					$strClass = Pattern::findClass($objSubPattern->type);
						
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
		if ($this->subPatternType == 'options')
		{
			$strPreview = '<div class="" style="padding-top:10px;"><h3 style="margin: 0;"><label>' . $this->label . '</label></h3>';
			$strPreview .= '<select class="tl_select" style="width: 412px;" onchange="$$(\'.tl_select_container_' . $this->id . '\').hide();$$(\'.tl_select_container_' . $this->id . '_\' + this.options[this.selectedIndex].value).show();">';

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
					$strPreview .=  '<div class="tl_select_container_' . $this->id . ' tl_select_container_' . $this->id . '_' . $arrOption['value'] . '" style="display: ' . (($arrOption['default']) ? 'block' : 'none'). ';">';	
						
					$colSubPattern = \ContentPatternModel::findPublishedByPidAndTableAndSubOption($this->id, 'tl_content_subpattern', $arrOption['value'], array('order'=>'sorting ASC'));
					
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
		else{
			
			$strPreview =  '<div class="tl_checkbox_single_container"><input class="tl_checkbox" value="1" type="checkbox" onclick="$$(\'.tl_checkbox_container_' . $this->id . '\').toggle();"> <label onclick="$$(\'.tl_checkbox_container_' . $this->id . '\').toggle();">' . $this->label . '</label><p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';	
			$strPreview .=  '<div class="tl_checkbox_container_' . $this->id . '" style="display: none;">';	

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
		
						$strPreview .= $objSubPatternClass->view();
					}
				}
			}

			$strPreview .=  '</div>';	

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
			if ($this->Value->selectField)
			{
				// add select option value (instead of the alias) to the value mapper
				$this->arrMapper[] = substr($this->Value->selectField,1);


				// get the pattern model collection
				$colSubPattern = \ContentPatternModel::findPublishedByPidAndTableAndSubOption($this->id, 'tl_content_subpattern', substr($this->Value->selectField, 1));

				if ($colSubPattern === null)
				{
					return;
				}
				
				// prepare values for every pattern
				foreach($colSubPattern as $objSubPattern)
				{
					// don´t show system pattern
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
					// don´t show system pattern
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
