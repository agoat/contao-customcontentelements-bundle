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

use Contao\Model;


class ContentPatternModel extends Model
{

	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_content_pattern';

	
	/**
	 * Find all pattern by their Pids (section Ids)
	 *
	 * @param integer $arrPids        An array of section IDs
	 * @param array   $arrOptions     An optional options array
	 *
	 * @return \Model\Collection|\ContentPatternModel|null A collection of models or null if there are no content elements
	 */
	public static function findByPids($arrPids, array $arrOptions=array())
	{
		$t = static::$strTable;
		$arrColumns = array("$t.pid IN (" . implode(',', array_map('intval', $arrPids)) . ")");

		return static::findBy($arrColumns, null, $arrOptions);
	}

	/**
	 * Find all pattern by their Pids (section Ids)
	 *
	 * @param integer $arrPids        An array of section IDs
	 * @param array   $arrOptions     An optional options array
	 *
	 * @return \Model\Collection|\ContentPatternModel|null A collection of models or null if there are no content elements
	 */
	public static function findByPidAndTable($intPid, $strParentTable='tl_content_blocks', array $arrOptions=array())
	{
		$t = static::$strTable;
		
		
		// Also handle empty ptable fields
		if ($strParentTable == 'tl_content_blocks')
		{
			$arrColumns = array("$t.pid=? AND ($t.ptable=? OR $t.ptable='')");
		}
		else
		{
			$arrColumns = array("$t.pid=? AND $t.ptable=?");
		}
		
		if (!isset($arrOptions['order']))
		{
			$arrOptions['order'] = "$t.sorting";
		}

		return static::findBy($arrColumns, array($intPid, $strParentTable), $arrOptions);
	}

	/**
	 * Find all pattern by their Pids (section Ids)
	 *
	 * @param integer $arrPids        An array of section IDs
	 * @param array   $arrOptions     An optional options array
	 *
	 * @return \Model\Collection|\ContentPatternModel|null A collection of models or null if there are no content elements
	 */
	public static function findPublishedByPidAndTable($intPid, $strParentTable='tl_content_blocks', array $arrOptions=array())
	{
		$t = static::$strTable;
		
		
		// Also handle empty ptable fields
		if ($strParentTable == 'tl_content_blocks')
		{
			$arrColumns = array("$t.pid=? AND ($t.ptable=? OR $t.ptable='') AND $t.invisible=''");
		}
		else
		{
			$arrColumns = array("$t.pid=? AND $t.ptable=? AND $t.invisible=''");
		}
		
		if (!isset($arrOptions['order']))
		{
			$arrOptions['order'] = "$t.sorting";
		}

		return static::findBy($arrColumns, array($intPid, $strParentTable), $arrOptions);
	}



}
