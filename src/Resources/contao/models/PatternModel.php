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



class PatternModel extends Model
{

	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_pattern';


	/**
	 * Find all pattern by their Pids (section Ids)
	 *
	 * @param integer $arrPids        An array of section IDs
	 * @param array   $arrOptions     An optional options array
	 *
	 * @return \Model\Collection|\ContentPatternModel|null A collection of models or null if there are no content elements
	 */
	 public static function findByPid($intPid, array $arrOptions=array())
	{
		$t = static::$strTable;
		

		// Set ptable
		$arrColumns = array("$t.pid=? AND ($t.ptable='tl_elements' OR $t.ptable='')");
		
		if (!isset($arrOptions['order']))
		{
			$arrOptions['order'] = "$t.sorting";
		}

		return static::findBy($arrColumns, $intPid, $arrOptions);
	}


	/**
	 * Find all pattern by their Pids (section Ids)
	 *
	 * @param integer $arrPids        An array of section IDs
	 * @param array   $arrOptions     An optional options array
	 *
	 * @return \Model\Collection|\ContentPatternModel|null A collection of models or null if there are no content elements
	 */
	public static function findByPidAndTable($intPid, $strParentTable, array $arrOptions=array())
	{
		$t = static::$strTable;
		
		
		// Also handle empty ptable fields
		if ($strParentTable == 'tl_elements')
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
	 public static function findVisibleByPid($intPid, array $arrOptions=array())
	{
		$t = static::$strTable;
		

		// Set ptable
		$arrColumns = array("$t.pid=? AND ($t.ptable='tl_elements' OR $t.ptable='') AND $t.invisible=''");
		
		if (!isset($arrOptions['order']))
		{
			$arrOptions['order'] = "$t.sorting";
		}

		return static::findBy($arrColumns, $intPid, $arrOptions);
	}

	 
	/**
	 * Find all pattern by their Pids (section Ids)
	 *
	 * @param integer $arrPids        An array of section IDs
	 * @param array   $arrOptions     An optional options array
	 *
	 * @return \Model\Collection|\ContentPatternModel|null A collection of models or null if there are no content elements
	 */
	 public static function findVisibleByPidAndTable($intPid, $strParentTable, array $arrOptions=array())
	{
		$t = static::$strTable;
		
		
		// Also handle empty ptable fields
		if ($strParentTable == 'tl_elements')
		{
			$arrColumns = array("$t.pid=? AND ($t.ptable=? OR $t.ptable='') AND $t.invisible=''");
		}
		else
		{
			$arrColumns = array("$t.pid=? AND ($t.ptable=? OR $t.ptable='') AND $t.invisible=''");
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
	 public static function findVisibleByPidAndTableAndOption($intPid, $strParentTable, $strSubPatternOption, array $arrOptions=array())
	{
		$t = static::$strTable;
		
		
		// Also handle empty ptable fields
		if ($strParentTable == 'tl_elements')
		{
			$arrColumns = array("$t.pid=? AND ($t.ptable=? OR $t.ptable='') AND $t.suboption=? AND $t.invisible=''");
		}
		else
		{
			$arrColumns = array("$t.pid=? AND ($t.ptable=? OR $t.ptable='') AND $t.suboption=? AND $t.invisible=''");
		}
		
		if (!isset($arrOptions['order']))
		{
			$arrOptions['order'] = "$t.sorting";
		}

		return static::findBy($arrColumns, array($intPid, $strParentTable, $strSubPatternOption), $arrOptions);
	}


	/**
	 * Count all pattern by their Pid and Type
	 *
	 * @param integer $arrPids        An array of section IDs
	 * @param array   $arrOptions     An optional options array
	 *
	 * @return \Model\Collection|\ContentPatternModel|null A collection of models or null if there are no content elements
	 */
	 public static function countByPidAndType($intPid, $strType, array $arrOptions=array())
	{
		$t = static::$strTable;
		
		$arrColumns = array("$t.pid=? AND ($t.ptable='tl_elements' OR $t.ptable='') AND $t.type=?");
		
		return static::countBy($arrColumns, array($intPid, $strType), $arrOptions);
	}



}
