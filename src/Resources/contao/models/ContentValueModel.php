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


class ContentValueModel extends Model
{

	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_content_value';


	/**
	 * Find all values by their content id
	 *
	 * @param integer $arrPids        An array of section IDs
	 * @param array   $arrOptions     An optional options array
	 *
	 * @return \Model\Collection|\ContentPatternModel|null A collection of models or null if there are no content elements
	 */
	public static function findByCid($varCid, array $arrOptions=array())
	{
		$t = static::$strTable;
		
		$arrOptions = array_merge
		(		
			array
			(
				'column'	=>	array("$t.cid=?"),
				'value'		=>	array($varCid),
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
	public static function findByCidandPid($varCid, $varPid, array $arrOptions=array())
	{
		$t = static::$strTable;
		
		$arrOptions = array_merge
		(		
			array
			(
				'column'	=>	array("$t.cid=?","$t.pid=?"),
				'value'		=>	array($varCid, $varPid),
				'order'		=>	"$t.rid ASC",
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
	public static function findByCidandPidandRid($varCid, $varPid, $varRid, array $arrOptions=array())
	{
		$t = static::$strTable;
		
		$arrOptions = array_merge
		(		
			array
			(
				'column'	=>	array("$t.cid=?","$t.pid=?","$t.rid=?"),
				'value'		=>	array($varCid, $varPid, $varRid),
				'return'	=>	'Model',
				'limit'		=>	1
			),
			$arrOptions
		);

		return static::find($arrOptions);
	}

}
