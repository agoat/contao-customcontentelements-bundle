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



class DataModel extends Model
{

	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_data';


	/**
	 * Find all values by their content and pattern id
	 *
	 * @param integer $arrPids        An array of section IDs
	 * @param array   $arrOptions     An optional options array
	 *
	 * @return \Model\Collection|\ContentPatternModel|null A collection of models or null if there are no content elements
	 */
	public static function findByPid($varPid, array $arrOptions=array())
	{
		$t = static::$strTable;
		
		$arrOptions = array_merge
		(		
			array
			(
				'column'	=>	array("$t.pid=?","$t.parent=0"),
				'value'		=>	array($varPid)
			),
			$arrOptions
		);

		return static::find($arrOptions);
	}

	
	/**
	 * Find all values by their content and pattern id
	 *
	 * @param integer $arrPids        An array of section IDs
	 * @param array   $arrOptions     An optional options array
	 *
	 * @return \Model\Collection|\ContentPatternModel|null A collection of models or null if there are no content elements
	 */
	public static function findByPidAndPattern($varPid, $strPattern, array $arrOptions=array())
	{
		$t = static::$strTable;
		
		$arrOptions = array_merge
		(		
			array
			(
				'column'	=>	array("$t.pid=?","$t.pattern=?","$t.parent=0"),
				'value'		=>	array($varPid, $strPattern)
			),
			$arrOptions
		);

		return static::find($arrOptions);
	}

	
	/**
	 * Find all values by their content and pattern id
	 *
	 * @param integer $arrPids        An array of section IDs
	 * @param array   $arrOptions     An optional options array
	 *
	 * @return \Model\Collection|\ContentPatternModel|null A collection of models or null if there are no content elements
	 */
	public static function findByPidAndParent($varPid, $intParent, array $arrOptions=array())
	{
		$t = static::$strTable;
		
		$arrOptions = array_merge
		(		
			array
			(
				'column'	=>	array("$t.pid=?","$t.parent=?"),
				'value'		=>	array($varPid, $intParent)
			),
			$arrOptions
		);

		return static::find($arrOptions);
	}
	

	/**
	 * Find all values by their content, pattern and replica id
	 *
	 * @param integer $arrPids        An array of section IDs
	 * @param array   $arrOptions     An optional options array
	 *
	 * @return \Model\Collection|\ContentPatternModel|null A collection of models or null if there are no content elements
	 */
	public static function findByPidAndPatternAndParent($varPid, $strPattern, $intParent, array $arrOptions=array())
	{
		$t = static::$strTable;
		
		$arrOptions = array_merge
		(		
			array
			(
				'column'	=>	array("$t.pid=?","$t.pattern=?","$t.parent=?"),
				'value'		=>	array($varPid, $strPattern, $intParent)
			),
			$arrOptions
		);

		return static::find($arrOptions);
	}

	
	
}
