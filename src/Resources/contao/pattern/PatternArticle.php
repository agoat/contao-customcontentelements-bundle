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


class PatternArticle extends Pattern
{


	/**
	 * generate the DCA construct
	 */
	public function construct()
	{
		$arrNodes = false;
		
		if ($this->insideRoot)
		{
			$objElement = \ContentModel::findById($this->cid);
			$objArticle = \ArticleModel::findById((int) $objElement->pid);
			$objPage = \PageModel::findWithDetails((int) $objArticle->pid);

			if (!$this->insideLang)
			{
				$objRootPages = \PageModel::findByDns($objPage->domain);
				
				$arrNodes = $objRootPages->fetchEach('id');
			}
			else
			{
				$arrNodes = array($objPage->rootId);
			}
		}
		
		if ($this->multiArticle)
		{
			$this->generateDCA('multiArticle', array
			(
				'inputType' => 'articleTree',
				'label'		=> array($this->label, $this->description),
				'eval'		=> array
				(
					'multiple'		=>	true,				
					'fieldType'		=>	'checkbox', 
					'orderField'	=>	$this->virtualFieldName('orderArticle'),
					'mandatory'		=> ($this->mandatory) ? true : false, 
					'rootNodes'		=>	$arrNodes,
					'tl_class'		=> 'clr'
				),
				'load_callback'		=> array
				(
					array('tl_content_contentblocks', 'prepareOrderPageValue'),
				),
				'save_callback'		=> array
				(
					array('tl_content_contentblocks', 'saveOrderPageValue'),
				),
			));
		}
		else
		{
			$this->generateDCA('singleArticle', array
			(
				'inputType' => 'articleTree',
				'label'		=> array($this->label, $this->description),
				'eval'		=> array
				(
					'fieldType'		=>	'radio', 
					'mandatory'		=> ($this->mandatory) ? true : false, 
					'rootNodes'		=>	$arrNodes,
					'tl_class'		=> 'clr'
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

		if ($this->multiArticle)
		{
			$strPreview .= '<li><img src="system/themes/flexible/icons/articles.svg" width="18" height="18" alt=""> Articletitle1</li><li><img src="system/themes/flexible/icons/articles.svg" width="18" height="18" alt=""> Articletitle2</li><li><img src="system/themes/flexible/icons/articles.svg" width="18" height="18" alt=""> Articletitle3</li>';				
		}
		else
		{
			$strPreview .= '<li><img src="system/themes/flexible/icons/articles.svg" width="18" height="18" alt=""> Articletitle</li>';				
		}

		$strPreview .= '</ul><p><a href="javascript:void(0);" class="tl_submit">Change selection</a></p></div><p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';

		return $strPreview;
	}


	/**
	 * prepare data for the frontend template 
	 */
	public function compile()
	{
		if ($this->multiArticle)
		{
			$objArticles = \ArticleModel::findMultipleByIds(\StringUtil::deserialize($this->Value->multiArticle));

			// Return if there are no pages
			if ($objArticles === null)
			{
				return;
			}

			$arrArticles = array();

			// Sort the array keys according to the given order
			if ($this->Value->orderArticle != '')
			{
				$tmp = \StringUtil::deserialize($this->Value->orderArticle);

				if (!empty($tmp) && is_array($tmp))
				{
					$arrArticles = array_map(function () {}, array_flip($tmp));
				}
			}

			// Add the items to the pre-sorted array
			while ($objArticles->next())
			{
				$arrArticles[$objArticles->id] = $objArticles->row();
			}

			$arrArticles = array_values(array_filter($arrArticles));
			
			$this->writeToTemplate($arrArticles);

			}
		else
		{
			if (($objArticle = \ArticleModel::findById($this->Value->singleArticle)) !== null)
			{
				$this->writeToTemplate($objArticle->row());
			}
		}

	}
}
