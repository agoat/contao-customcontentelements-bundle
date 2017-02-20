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



// table callbacks
$GLOBALS['TL_DCA']['tl_page']['config']['oncopy_callback'][] = array('tl_page_contentblocks', 'copyRelatedValues');
$GLOBALS['TL_DCA']['tl_page']['config']['ondelete_callback'][] = array('tl_page_contentblocks', 'deleteRelatedValues');

	
class tl_page_contentblocks extends tl_page
{
	/**
	 * copy related Values from the content elements when an article is copied
	 */
	public function copyRelatedValues ($intId, $dc)
	{
		$colOrigArticles = \ArticleModel::findByPid($dc->id, array('order'=>'sorting'));
		$colNewArticles = \ArticleModel::findByPid($intId, array('order'=>'sorting'));
        
		if ($colOrigArticles !== null && $colNewArticles!== null)
		{
			// Call the oncopy_callback for every article element (use the model as the datacontainer)
			while ($colOrigArticles->next() && $colNewArticles->next())
			{
				\tl_article_contentblocks::copyRelatedValues($colNewArticles->current()->id, $colOrigArticles->current());
			}
		}
	}


	/**
	 * delete related Values from the content elements when an article is deleted
	 */
	public function deleteRelatedValues ($dc, $intUndoId)
	{
		$db = Database::getInstance();
		
		// get the undo database row
		$objUndo = $db->prepare("SELECT data FROM tl_undo WHERE id=?")
					  ->execute($intUndoId) ;

		$arrData = \StringUtil::deserialize($objUndo->fetchAssoc()[data]);

		$colArticles = \ArticleModel::findByPid($dc->activeRecord->id);
		
		if ($colArticles === null)
		{
			return;
		}
		
		foreach ($colArticles as $objArticle)
		{
			$colContent = \ContentModel::findByPid($objArticle->id);
			
			if ($colContent === null)
			{
				return;
			}
			
			foreach ($colContent as $objContent)
			{
				$colValues = \ContentValueModel::findByCid($objContent->id);
			
				if ($colValues === null)
				{
					continue;
				}
				
				foreach ($colValues as $objValue)
				{
					// get value row(s)
					$arrData['tl_content_value'][] = $objValue->row();
		
					$objValue->delete();
				}
				
			}
		}
		
		// save back to the undo database row
		$db->prepare("UPDATE tl_undo SET data=? WHERE id=?")
		   ->execute(serialize($arrData), $intUndoId);
	}
}
