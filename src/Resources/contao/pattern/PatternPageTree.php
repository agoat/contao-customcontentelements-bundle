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


class PatternPageTree extends Pattern
{


	/**
	 * generate the DCA construct
	 */
	public function construct()
	{
		
		if ($this->multiPage)
		{
			// the multiPage field
			$this->generateDCA('multiPage', array
			(
				'inputType' =>	'pageTree',
				'label'		=>	array($this->label, $this->description),
				'eval'     	=> 	array
				(
					'multiple'		=>	true,				
					'fieldType'		=>	'checkbox', 
					'orderField'	=>	$this->virtualFieldName('orderPage'),
					'files'			=>	true,
					'mandatory'		=>	($this->mandatory) ? true : false, 
					'tl_class'		=>	'clr',
				),
				'load_callback'		=> array
				(
					array('tl_content_elements', 'prepareOrderPageValue'),
				),
				'save_callback'		=> array
				(
					array('tl_content_elements', 'saveOrderPageValue'),
				),
			));
		}
		else
		{
			// the multiPage field
			$this->generateDCA('singlePage', array
			(
				'inputType' =>	'pageTree',
				'label'		=>	array($this->label, $this->description),
				'eval'     	=> 	array
				(
					'fieldType'		=>	'radio', 
					'extensions' 	=>	$extensions,
					'mandatory'		=>	($this->mandatory) ? true : false, 
					'tl_class'		=>	'clr',
				),
			));
			
		}
		
	}
	

	/**
	 * Generate backend output
	 */
	public function view()
	{
		$strPreview = '<div class="inline" style="padding-top:10px;"><h3 style="margin: 0;"><label>' . $this->label . '</label></h3><div class="selector_container"><ul>';
		$strPreview .= '<li><img src="system/themes/flexible/icons/regular.svg" width="18" height="18" alt=""> Pagetitle</li>';				
		$strPreview .= '</ul><p><a href="javascript:void(0);" class="tl_submit">Change selection</a></p></div><p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';

		return $strPreview;
	}


	/**
	 * prepare data for the frontend template 
	 */
	public function compile()
	{
		// prepare value(s)
		if ($this->multiPage)
		{
			$objPages = \PageModel::findMultipleByIds(\StringUtil::deserialize($this->Value->multiPage));

			// Return if there are no pages
			if ($objPages === null)
			{
				return;
			}

			$arrPages = array();

			// Sort the array keys according to the given order
			if ($this->Value->orderPage != '')
			{
				$tmp = \StringUtil::deserialize($this->Value->orderPage);

				if (!empty($tmp) && is_array($tmp))
				{
					$arrPages = array_map(function () {}, array_flip($tmp));
				}
			}

			// Add the items to the pre-sorted array
			while ($objPages->next())
			{
				$arrPages[$objPages->id] = $objPages->row();
			}

			$arrPages = array_values(array_filter($arrPages));
			
			$this->writeToTemplate($arrPages);
		}
		else
		{
			if (($objPage = \PageModel::findById($this->Value->singlePage)) !== null)
			{
                $this->writeToTemplate($objPage->row());
            }
		}
	}
}
