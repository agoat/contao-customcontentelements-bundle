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


class ElementsModel extends Model
{

	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_elements';



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
	 * Find published content blocks by theme id
	 *
	 * @param mixed   $varId      The numeric ID or alias name
	 * @param integer $intPid     The page ID
	 * @param array   $arrOptions An optional options array
	 *
	 * @return static The model or null if there is no article
	 */
	public function findPublishedByAlias($strAlias, array $arrOptions=array())
	{
		$t = static::$strTable;
		$arrColumns = array("$t.alias=? AND $t.invisible=''");
	
		return static::findBy($arrColumns, $strAlias, $arrOptions);
	}


	/**
	 * Find published content blocks by theme id
	 *
	 * @param mixed   $varId      The numeric ID or alias name
	 * @param integer $intPid     The page ID
	 * @param array   $arrOptions An optional options array
	 *
	 * @return static The model or null if there is no article
	 */
	public function findFirstPublishedElementByPid($intPid, array $arrOptions=array())
	{
		$t = static::$strTable;
		$arrColumns = array("$t.pid=? AND $t.invisible='' AND $t.type='element'");
	
		return static::findOneBy($arrColumns, $intPid, $arrOptions);
	}


	/**
	 * Find published content blocks by theme id
	 *
	 * @param mixed   $varId      The numeric ID or alias name
	 * @param integer $intPid     The page ID
	 * @param array   $arrOptions An optional options array
	 *
	 * @return static The model or null if there is no article
	 */
	public function findDefaultPublishedElementByPid($intPid, array $arrOptions=array())
	{
		$t = static::$strTable;
		$arrColumns = array("$t.defaultType=1 AND $t.pid=? AND $t.invisible='' AND $t.type='element'");
	
		return static::findOneBy($arrColumns, $intPid, $arrOptions);
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
