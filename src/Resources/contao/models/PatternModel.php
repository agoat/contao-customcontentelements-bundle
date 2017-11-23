<?php

/*
 * Custom content elements extension for Contao Open Source CMS.
 *
 * @copyright  Arne Stappen (alias aGoat) 2017
 * @package    contao-contentelements
 * @author     Arne Stappen <mehh@agoat.xyz>
 * @link       https://agoat.xyz
 * @license    LGPL-3.0
 */

namespace Contao;


/**
 * Reads and writes element pattern
 */
class PatternModel extends Model
{

	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_pattern';


	/**
	 * Find all pattern by their pid (elements id)
	 *
	 * @param integer $intPid     The content element id
	 * @param array   $arrOptions An optional options array
	 *
	 * @return \Model\Collection|\PatternModel|null A collection of models or null if there are no pattern
	 */
	 public static function findByPid($intPid, array $arrOptions=array())
	{
		$t = static::$strTable;

		$arrColumns = array("$t.pid=? AND ($t.ptable='tl_elements' OR $t.ptable='')");
		
		if (!isset($arrOptions['order']))
		{
			$arrOptions['order'] = "$t.sorting";
		}

		return static::findBy($arrColumns, $intPid, $arrOptions);
	}


	/**
	 * Find all pattern by their pid (elements id) and parent table
	 *
	 * @param integer $intPid         The content element id
	 * @param string  $strParentTable The parent table name
	 * @param array   $arrOptions     An optional options array
	 *
	 * @return \Model\Collection|\PatternModel|null A collection of models or null if there are no pattern
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
	 * Find visible pattern by their pid (elements id)
	 *
	 * @param integer $intPid     The content element id
	 * @param array   $arrOptions An optional options array
	 *
	 * @return \Model\Collection|\PatternModel|null A collection of models or null if there are no pattern
	 */
	 public static function findVisibleByPid($intPid, array $arrOptions=array())
	{
		$t = static::$strTable;
		
		$arrColumns = array("$t.pid=? AND ($t.ptable='tl_elements' OR $t.ptable='') AND $t.invisible=''");
		
		if (!isset($arrOptions['order']))
		{
			$arrOptions['order'] = "$t.sorting";
		}

		return static::findBy($arrColumns, $intPid, $arrOptions);
	}

	 
	/**
	 * Find visible pattern by their pid (elements id) and parent table
	 *
	 * @param integer $intPid         The content element id
	 * @param string  $strParentTable The parent table name
	 * @param array   $arrOptions     An optional options array
	 *
	 * @return \Model\Collection|\PatternModel|null A collection of models or null if there are no pattern
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
	 * Find visible pattern by their pid (elements id), parent table and subpattern option
	 *
	 * @param integer $intPid              The content element id
	 * @param string  $strParentTable      The parent table name
	 * @param string  $strSubPatternOption The subpattern option
	 * @param array   $arrOptions          An optional options array
	 *
	 * @return \Model\Collection|\PatternModel|null A collection of models or null if there are no pattern
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
	 * Count all pattern by their pid (elements id) and type
	 *
	 * @param integer $intPid     The content element id
	 * @param string  $strType    The pattern type
	 * @param array   $arrOptions An optional options array
	 *
	 * @return integer The number of matching rows
	 */
	 public static function countByPidAndType($intPid, $strType, array $arrOptions=array())
	{
		$t = static::$strTable;
		
		$arrColumns = array("$t.pid=? AND ($t.ptable='tl_elements' OR $t.ptable='') AND $t.type=?");
		
		return static::countBy($arrColumns, array($intPid, $strType), $arrOptions);
	}



}
