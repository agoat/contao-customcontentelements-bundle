<?php

/*
 * Custom content elements extension for Contao Open Source CMS.
 *
 * @copyright  Arne Stappen (alias aGoat) 2017
 * @package    contao-customcontentelements
 * @author     Arne Stappen <mehh@agoat.xyz>
 * @link       https://agoat.xyz
 * @license    LGPL-3.0
 */

namespace Contao;


/**
 * Reads and writes content data
 */
class DataModel extends Model
{

	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_data';


	/**
	 * Find all values by their pid (content id)
	 *
	 * @param integer $varPid     The content id
	 * @param array   $arrOptions An optional options array
	 *
	 * @return \Model\Collection|\DataModel|null A collection of models or null if there are no data
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
	 * Find all values by their pid (content id) and pattern type
	 *
	 * @param integer $arrPids    The content id
	 * @param string  $strPattern The pattern type
	 * @param array   $arrOptions An optional options array
	 *
	 * @return \Model\Collection|\DataModel|null A collection of models or null if there are no data
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
	 * Find all values by their pid (content id) and parent pattern id
	 *
	 * @param integer $arrPids    The content id
	 * @param integer $intParent  The parent pattern id
	 * @param array   $arrOptions An optional options array
	 *
	 * @return \Model\Collection|\DataModel|null A collection of models or null if there are no data
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
	 * Find all values by their pid (content id), pattern type and parent pattern id
	 *
	 * @param integer $arrPids    The content id
	 * @param string  $strPattern The pattern type
	 * @param integer $intParent  The parent pattern id
	 * @param array   $arrOptions An optional options array
	 *
	 * @return \Model\Collection|\DataModel|null A collection of models or null if there are no data
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
