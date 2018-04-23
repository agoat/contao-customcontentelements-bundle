<?php

/*
 * Custom content elements extension for Contao Open Source CMS.
 *
 * @copyright  Arne Stappen (alias aGoat) 2017
 * @package    contao-customcontentelements
 * @author     Arne Stappen <mehh@agoat.xyz>
 * @link       https://agoat.xyz
 * @license    LGPL-3.0
 */

namespace Agoat\CustomContentElementsBundle\Contao;

use Contao\StringUtil;


/**
 * Content element pattern "textfield"
 */
class PatternTextField extends Pattern
{
	/**
	 * Creates the DCA configuration
	 */
	public function create()
	{
		$class = ($this->classLong) ? 'long' : 'w50';
		$class .= ($this->classClr) ? ' clr' : '';
		$class .= ($this->picker) ? ' wizard' : '';
		
		// Input unit
		if (($this->picker == 'unit'))
		{
			$options = array();
			foreach (StringUtil::deserialize($this->units) as $arrOption)
			{
				$options[$arrOption['value']] = $arrOption['label'];
			}
		}

		// The text field
		$this->generateDCA(($this->picker != 'unit') ? ($this->multiple) ? 'multiTextField' : 'singleTextField' : 'inputUnit', array
		(
			'inputType' =>	($this->picker == 'unit') ? 'inputUnit' : 'text',
			'label'		=>	array($this->label, $this->description),
			'default'	=>	$this->defaultValue,
			'options'	=>	$options,
			'eval'		=>	array
			(
				'mandatory'		=>	($this->mandatory) ? true : false, 
				'minlength'		=>	$this->minlength, 
				'maxlength'		=>	$this->maxLength, 
				'tl_class'		=>	$class,
				'rgxp'			=>	$this->rgxp,
				'multiple'		=>	($this->multiple) ? true : false,
				'size'			=>	$this->multiple,
				'datepicker' 	=> 	($this->picker == 'datetime') ? true : false,
				'colorpicker' 	=> 	($this->picker == 'color') ? true : false,
				'isHexColor' 	=> 	($this->picker == 'color') ? true : false,
				'dcaPicker' 	=> 	($this->picker == 'link') ? true : false,
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
		$strPreview = '<div class="widget ' . (($this->classLong) ? 'long' : 'w50') . ((in_array($this->picker, array('datetime', 'color', 'link'))) ? ' wizard' : '') . ' widget"><h3><label>' . $this->label . '</label></h3>';

		if ($this->picker == 'unit')
		{
			$strPreview .= '<input class="tl_text_unit" value="' . $this->defaultValue . '" type="text">';
			$strPreview .= ' <select class="tl_select_unit">';

			$units = StringUtil::deserialize($this->units);
			foreach ($units as $unit)
			{
				$strPreview .= '<option value="' . $unit['value'] . '">' . $unit['label'] . '</option>';
			}

			$strPreview .= '</select>';
		}
		
		elseif ($this->multiple)
		{
			$strPreview .= '<div>';
			
			for ($i = 0; $i < $this->multiple; $i++)
			{
				$strPreview .= '<input class="tl_text_' . $this->multiple . '" value="' . $this->defaultValue . '" type="text"> ';
			}

			$strPreview .= '</div>';
		}
		
		else
		{
			$strPreview .= '<input class="tl_text" value="' . $this->defaultValue . '" type="text">';
		}
		
		switch ($this->picker)
		{
			case 'datetime':
				$strPreview .=  ' <img src="assets/datepicker/images/icon.svg" height="20" width="20">';
				break;

			case 'color':
				$strPreview .=  ' <img src="system/themes/flexible/icons/pickcolor.svg" height="16" width="16">';
				break;
			
			case 'link':
				$strPreview .=  ' <img src="system/themes/flexible/icons/pickpage.svg" height="16" width="16">';
				break;
		}
		
		$strPreview .= '<p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';	

		return $strPreview;
	}


	/**
	 * Prepare the data for the template
	 */
	public function compile()
	{
		$this->writeToTemplate(($this->picker != 'unit') ? ($this->multiple) ? StringUtil::deserialize($this->data->multiTextField) : $this->data->singleTextField : StringUtil::deserialize($this->data->inputUnit));	
	}
}
