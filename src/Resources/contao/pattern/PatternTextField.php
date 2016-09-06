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

use Agoat\ContentBlocks\Pattern;


class PatternTextField extends Pattern
{


	/**
	 * generate the DCA construct
	 */
	public function construct()
	{
		
		
		$class = ($this->classClr) ? 'w50 clr' : 'w50';
		$class = ($this->classLong) ? 'long clr' : $class;
		$class .= ($this->picker) ? ' wizard' : '';
		
		$wizard = ($this->picker == 'page') ? array(array('tl_content', 'pagePicker')) : false;
		
		// input unit
		if (($this->picker == 'unit'))
		{
			$options = array();
			foreach (deserialize($this->units) as $arrOption)
			{
				$options[$arrOption['value']] = $arrOption['label'];
			}
		}

		// the text field
		$this->generateDCA(($this->picker != 'unit') ? ($this->multiple) ? 'multiField' : 'textField' : 'inputUnit', array
		(
			'inputType' =>	($this->picker == 'unit') ? 'inputUnit' : 'text',
			'label'		=>	array($this->label, $this->description),
			'default'	=>	$this->defaultValue,
			'wizard'	=>	$wizard,
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
			),
		));
		
	}
	

	/**
	 * Generate backend output
	 */
	public function view()
	{
		$strPreview = '<div class="inline' . ((in_array($this->picker, array('datetime', 'color', 'page'))) ? ' wizard' : '') . '" style="padding-top:10px;"><h3><label>' . $this->label . '</label></h3>';

		if ($this->picker == 'unit')
		{
			$strPreview .= '<input class="tl_text_unit" value="' . $this->defaultValue . '" type="text">';
			$strPreview .= ' <select class="tl_select_unit">';

			$units = deserialize($this->units);
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
			
			case 'page':
				$strPreview .=  ' <img src="system/themes/flexible/icons/pickpage.svg" height="16" width="16">';
				break;
		}
		
		
		$strPreview .= '<p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';	

		return $strPreview;
	}


	/**
	 * prepare data for the frontend template 
	 */
	public function compile()
	{
		$this->writeToTemplate(($this->picker != 'unit') ? ($this->multiple) ? deserialize($this->Value->multiField) : $this->Value->textField : deserialize($this->Value->inputUnit));	
	}
	
}
