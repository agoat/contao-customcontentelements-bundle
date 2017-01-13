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
$GLOBALS['TL_DCA']['tl_article']['config']['oncopy_callback'][] = array('tl_article_contentblocks', 'copyRelatedValues');
$GLOBALS['TL_DCA']['tl_article']['config']['ondelete_callback'][] = array('tl_article_contentblocks', 'deleteRelatedValues');

	
class tl_article_contentblocks extends tl_article
{
	/**
	 * copy related Values from the content elements when an article is copied
	 */
	public function copyRelatedValues ($intId, $dc)
	{
		$colOrigContent = \ContentModel::findByPid($dc->id, array('order'=>'sorting'));
		$colNewContent = \ContentModel::findByPid($intId, array('order'=>'sorting'));

		// Call the oncopy_callback for every content element (use the model as the datacontainer)
		while ($colOrigContent->next() && $colNewContent->next())
		{
			\tl_content_contentblocks::copyRelatedValues($colNewContent->current()->id, $colOrigContent->current());
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

		$colContent = \ContentModel::findByPid($dc->activeRecord->id);
		
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

		// save back to the undo database row
		$db->prepare("UPDATE tl_undo SET data=? WHERE id=?")
		   ->execute(serialize($arrData), $intUndoId);
	}
}
