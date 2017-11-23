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


/**
 * Content element pattern "pagetree"
 */
class PatternPageTree extends Pattern
{
	/**
	 * Creates the DCA configuration
	 */
	public function create()
	{
		$arrNodes = false;
		
		if ($this->insideRoot)
		{
			$objContent = \ContentModel::findById($this->data->pid);

			$rootId = Controller::getRootPageId($objContent->ptable, $objContent->pid);

			if (null !== $rootId)
			{
				$objPage = \PageModel::findWithDetails($rootId);

				if ($this->insideLang || empty($objPage->domain))
				{
					$arrNodes = array($rootId);
				}
				else
				{
					$arrNodes = (array) \PageModel::findByDns($objPage->domain)->fetchEach('id');
				}
			}
		}
		
		if ($this->multiPage)
		{
			$this->generateDCA('multiPage', array
			(
				'inputType' =>	'pageTree',
				'label'		=>	array($this->label, $this->description),
				'eval'     	=> 	array
				(
					'multiple'		=>	true,				
					'fieldType'		=>	'checkbox', 
					'orderField'	=>	$this->virtualFieldName('orderPage'),
					'mandatory'		=>	($this->mandatory) ? true : false, 
					'rootNodes'		=>	$arrNodes,
					'tl_class'		=>	'clr'
				),
				'load_callback'		=> array
				(
					array('tl_content_elements', 'prepareOrderValue'),
				),
				'save_callback'		=> array
				(
					array('tl_content_elements', 'saveOrderValue'),
				),
			));

			// The orderPage field
			$this->generateDCA('orderPage', array(), false, false);
		}
		
		else
		{
			$this->generateDCA('singlePage', array
			(
				'inputType' =>	'pageTree',
				'label'		=>	array($this->label, $this->description),
				'eval'     	=> 	array
				(
					'fieldType'		=>	'radio', 
					'mandatory'		=>	($this->mandatory) ? true : false, 
					'rootNodes'		=>	$arrNodes,
					'tl_class'		=>	'clr'
				),
			));
		}
	}
	

	/**
	 * Generate the pattern preview
	 *
	 * @return string HTML code
	 */
	public function preview()
	{
		$strPreview = '<div class="widget clr"><h3 style="margin: 0;"><label>' . $this->label . '</label></h3><div class="selector_container"><ul>';

		if ($this->multiPage)
		{
			$strPreview .= '<li><img src="system/themes/flexible/icons/regular.svg" width="18" height="18" alt=""> Articletitle1</li><li><img src="system/themes/flexible/icons/regular.svg" width="18" height="18" alt=""> Articletitle2</li><li><img src="system/themes/flexible/icons/regular.svg" width="18" height="18" alt=""> Articletitle3</li>';				
		}
		else
		{
			$strPreview .= '<li><img src="system/themes/flexible/icons/regular.svg" width="18" height="18" alt=""> Articletitle</li>';				
		}

		$strPreview .= '</ul><p><a href="javascript:void(0);" class="tl_submit">Change selection</a></p></div><p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';

		return $strPreview;
	}


	/**
	 * Prepare the data for the template
	 */
	public function compile()
	{
		if ($this->multiPage)
		{
			$objPages = \PageModel::findMultipleByIds(\StringUtil::deserialize($this->data->multiPage));

			// Return if there are no pages
			if ($objPages === null)
			{
				return;
			}

			$arrPages = array();

			// Sort the array keys according to the given order
			if ($this->data->orderPage != '')
			{
				$tmp = \StringUtil::deserialize($this->data->orderPage);

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
			if (($objPage = \PageModel::findById($this->data->singlePage)) !== null)
			{
                $this->writeToTemplate($objPage->row());
            }
		}
	}
}
