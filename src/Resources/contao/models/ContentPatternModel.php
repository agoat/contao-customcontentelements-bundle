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
	public static function findPublishedByPid($varPid, array $arrOptions=array())
	{
		$t = static::$strTable;
		
		$arrOptions = array_merge
		(		
			array
			(
				'column'	=>	array("$t.pid=?", "$t.invisible=?"),
				'value'		=>	array($varPid, ''),
				'order'		=>	"$t.sorting ASC",
			),
			$arrOptions
		);

		return static::find($arrOptions);
	}



}
