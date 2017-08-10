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


class ContentBlocksModel extends \Contao\Model
{

	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_content_blocks';



	/**
	 * Find published content blocks by theme id
	 *
	 * @param mixed   $varId      The numeric ID or alias name
	 * @param integer $intPid     The page ID
	 * @param array   $arrOptions An optional options array
	 *
	 * @return static The model or null if there is no article
	 */
	public function findPublishedByPid($intPid, array $arrOptions=array())
	{
		$t = static::$strTable;
		$arrColumns = array("$t.pid=? AND $t.invisible=''");
	
		return static::findBy($arrColumns, $intPid, $arrOptions);
	}

	
	/**
	 * Find all published pattern for the particular content block
	 *
	 * @param mixed   $varId      The numeric ID or alias name
	 * @param integer $intPid     The page ID
	 * @param array   $arrOptions An optional options array
	 *
	 * @return static The model or null if there is no article
	 */
	public function getRelatedPattern(array $arrOptions=array())
	{
		// Get pattern from pattern model
		return \ContentPatternModel::findPublishedByPidAndTable($this->id);
	}
}
