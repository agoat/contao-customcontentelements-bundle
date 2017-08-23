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


class PatternArticle extends Pattern
{


	/**
	 * generate the DCA construct
	 */
	public function construct()
	{
		$this->import('BackendUser', 'User');
		
		$db = \Database::getInstance();
	
		$arrPids = array();
		$arrArticle = array();
		$arrRoot = $db->getChildRecords(0, 'tl_page');
	
		if ($this->insideRoot)
		{
			$objElement = $db->prepare("SELECT pid FROM tl_content WHERE id=?")
							 ->limit(1)
							 ->execute($this->cid);
			
			if ($objElement->numRows)
			{
				$objArticle = $db->prepare("SELECT pid FROM tl_article WHERE id=?")
								 ->limit(1)
								 ->execute($objElement->pid);
			}

			// Limit pages to the website root
			if ($objArticle->numRows)
			{
				$objPage = \PageModel::findWithDetails($objArticle->pid);
				$arrRoot = $db->getChildRecords($objPage->rootId, 'tl_page');
				array_unshift($arrRoot, $objPage->rootId);
			}
		}

		unset($objArticle);

		// Limit pages to the user's pagemounts
		if ($this->User->isAdmin)
		{
			$objArticle = $db->execute("SELECT a.id, a.pid, a.title, a.inColumn, p.title AS parent FROM tl_article a LEFT JOIN tl_page p ON p.id=a.pid" . (!empty($arrRoot) ? " WHERE a.pid IN(". implode(',', array_map('intval', array_unique($arrRoot))) .")" : "") . " ORDER BY parent, a.sorting");
		}
		else
		{
			foreach ($this->User->pagemounts as $id)
			{
				if (!in_array($id, $arrRoot))
				{
					continue;
				}

				$arrPids[] = $id;
				$arrPids = array_merge($arrPids, $this->Database->getChildRecords($id, 'tl_page'));
			}

			if (!empty($arrPids))
			{
				$objArticle = $db->execute("SELECT a.id, a.pid, a.title, a.inColumn, p.title AS parent FROM tl_article a LEFT JOIN tl_page p ON p.id=a.pid WHERE a.pid IN(". implode(',', array_map('intval', array_unique($arrPids))) .") ORDER BY parent, a.sorting");
			}
		}

		// Edit the result
		if ($objArticle->numRows)
		{
			\System::loadLanguageFile('tl_article');

			while ($objArticle->next())
			{
				$key = $objArticle->parent . ' (ID ' . $objArticle->pid . ')';
				$arrArticle[$key][$objArticle->id] = $objArticle->title . ' (' . ($GLOBALS['TL_LANG']['COLS'][$objArticle->inColumn] ?: $objArticle->inColumn) . ', ID ' . $objArticle->id . ')';
			}
		}

		$class = ($this->classClr) ? 'w50 clr' : 'w50';
		$class .= ($this->multiArticle) ? ' autoheight' : '';
		$class .= (!$this->multiArticle) ? ' wizard' : '';
		
		$wizard = (!$this->multiArticle) ? array(array('tl_content', 'editArticleAlias')) : false;
		
		// Add a selectField with the articles as options
		$this->generateDCA(($this->multiArticle) ? 'multiSelectField' : 'singleSelectField', array
		(
			'inputType' => 'select',
			'label'		=> array($this->label, $this->description),
			'options'	=> $arrArticle,
			'wizard' 	=> $wizard,
			'eval'		=> array
			(
				'mandatory'				=> ($this->mandatory) ? true : false, 
				'includeBlankOption'	=> ($this->mandatory) ? false : true,
				'submitOnChange'		=> ($this->multiArticle) ? false : true,
				'multiple'				=> ($this->multiArticle) ? true : false,
				'chosen'				=> true,
				'tl_class'				=> $class,
			),
		));
	}
	

	/**
	 * Generate backend output
	 */
	public function view()
	{
		$strPreview = '<div class="" style="padding-top:10px;"><h3 style="margin: 0;"><label>' . $this->label . '</label></h3>';
		$strPreview .= '<select class="tl_select" style="width: 412px;">';
		$strPreview .= '<optgroup label="&nbsp;Page1">';
		$strPreview .= '<option value="article1">Article1</option>';
		$strPreview .= '<option value="article1">Article2</option>';
		$strPreview .= '</optgroup>';
		$strPreview .= '<optgroup label="&nbsp;Page2">';
		$strPreview .= '<option value="article1">Article3</option>';
		$strPreview .= '<option value="article1">Article4</option>';
		$strPreview .= '<option value="article1">Article5</option>';$strPreview .= '</optgroup>';
		$strPreview .= '</select><p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';
		
		return $strPreview;	
	}


	/**
	 * prepare data for the frontend template 
	 */
	public function compile()
	{
		if ($this->multiArticle)
		{
			$objArticles = \ArticleModel::findMultipleByIds(\StringUtil::deserialize($this->data->multiSelectField));

			// Return if there are no pages
			if ($objArticles === null)
			{
				return;
			}

			$arrArticles = array();

			// Add the items to the pre-sorted array
			while ($objArticles->next())
			{
				$arrArticles[] = $objArticles->row();
			}

			$arrArticles = array_values(array_filter($arrArticles));
			
			$this->writeToTemplate($arrArticles);
		}
		else
		{
			if (($objArticle = \ArticleModel::findById($this->data->selectField)) !== null)
			{
				$this->writeToTemplate($objArticle->row());
			}
		}

	}
}
