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



	
class tl_news_contentblocks extends Backend
{

	
	/**
	 * copy related Values from the content elements when an article is copied
	 */
	public function copyRelatedValues ($intId, $dc)
	{
		
		$colOrigContent = \ContentModel::findByPid($dc->id, array('order'=>'sorting ASC'));
		$colNewContent = \ContentModel::findByPid($intId, array('order'=>'sorting ASC'));

		while ($colOrigContent->next())
		{
			$colNewContent->next();

			$colValues = \ContentValueModel::findByCid($colOrigContent->id);

			if ($colValues === null)
			{
				continue;
			}
		
			foreach ($colValues as $objValue)
			{
				$objNewValue = clone $objValue;
				$objNewValue->cid = $colNewContent->id; // we assume that the sequence is the same as the original
				$objNewValue->save();
			} 
		
		}

	}

	/**
	 * delete related Values from the content elements when an article is deleted
	 */
	public function deleteRelatedValues ($dc, $intUndoId)
	{
		
		$colContent = \ContentModel::findByPid($dc->activeRecord->id);
		
		if ($colContent === null)
		{
			return;
		}

		// get the undo database row
		$objUndo = $this->Database->prepare("SELECT data FROM tl_undo WHERE id=?")
								  ->execute($intUndoId) ;

		$arrData = deserialize($objUndo->fetchAssoc()[data]);

		
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

	
		// save to the undo database row
		$this->Database->prepare("UPDATE tl_undo SET data=? WHERE id=?")
					   ->execute(serialize($arrData), $intUndoId);

	}

}
