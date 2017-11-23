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

namespace Agoat\ContentElements;

use Contao\StringUtil;


/**
 * Content element pattern "selectfield"
 */
class PatternSelectField extends Pattern
{
	/**
	 * Creates the DCA configuration
	 */
	public function create()
	{
		$class = ($this->classClr) ? 'w50 clr' : 'w50';
		$class .= ($this->multiSelect) ? ' autoheight' : '';

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
				$default = 'v'.$arrOption['value']; // Add a alphabetic character to allow numeric values
			}	
			
			$arrOptions[$strGroup]['v'.$arrOption['value']] = $arrOption['label'];
		}
		
		// No groups 
		if (count($arrOptions) < 2)
		{
			$arrOptions = array_values($arrOptions)[0];
		}

		// Generate a select field
		$this->generateDCA(($this->multiSelect) ? 'multiSelectField' : 'singleSelectField', array
		(
			'inputType' 	=>	'select',
			'label'			=>	array($this->label, $this->description),
			'default'		=>	$default,
			'options'		=>	$arrOptions,
			'eval'			=>	array
			(
				'mandatory'				=> ($this->mandatory) ? true : false, 
				'includeBlankOption'	=> ($this->blankOption) ? true : false,
				'multiple'				=> ($this->multiSelect) ? true : false,
				'chosen'				=> true,
				'tl_class'				=> $class,
			),
		));
	}
	

	/**
	 * Generate the pattern preview
	 *
	 * @return string HTML code
	 */
	public function preview()
	{
		$strPreview = '<div class="w50 widget" style="padding-top:10px;"><h3 style="margin: 0;"><label>' . $this->label . '</label></h3>';
		$strPreview .= '<select class="tl_select" style="width: 412px;">';

		if (is_array($arrOptions = \StringUtil::deserialize($this->options)))
		{
			foreach ($arrOptions as $arrOption)
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
		}
			
		if ($blnOpenGroup)
		{
			$strPreview .= '</optgroup>';
		}

		$strPreview .= '</select><p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';
		return $strPreview;	
	}


	/**
	 * Prepare the data for the template
	 */
	public function compile()
	{
		if ($this->multiSelect)
		{
			if (is_array($arrData = \StringUtil::deserialize($this->data->multiSelectField)))
			{
				foreach ($arrData as &$data)
				{
					$data = substr($data,1); // Remove the alphabetic character
				}
				
				$this->writeToTemplate($arrData);
			}
		}
		else
		{
			$this->writeToTemplate(substr($this->data->singleSelectField,1)); // Remove the alphabetic character
		}
	}
}
