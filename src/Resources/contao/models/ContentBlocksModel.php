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


namespace Contao;


/**
 * Reads and writes news
 *
 * @property integer $id
 * @property integer $pid
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class ContentBlocksModel extends \Model
{

	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_content_blocks';



	/**
	 * Find an article by its ID or alias and its page
	 *
	 * @param mixed   $varId      The numeric ID or alias name
	 * @param integer $intPid     The page ID
	 * @param array   $arrOptions An optional options array
	 *
	 * @return static The model or null if there is no article
	 */
	public function getRelatedPattern(array $arrOptions=array())
	{
		// get pattern from pattern model
		return \ContentPatternModel::findByPid($this->id);
		
	}


}
