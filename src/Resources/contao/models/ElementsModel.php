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
 * Reads and writes content elements
 */
class ElementsModel extends Model
{

	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_elements';



	/**
	 * Find published content elements by their pid (theme id)
	 *
	 * @param integer $intPid     The layout id
	 * @param array   $arrOptions An optional options array
	 *
	 * @return \Model\Collection|\ElementsModel|null A collection of models or null if there are no content elements
	 */
	public function findPublishedByPid($intPid, array $arrOptions=array())
	{
		$t = static::$strTable;

		$arrColumns = array("$t.pid=? AND $t.invisible=''");
	
		if (!isset($arrOptions['order']))
		{
			$arrOptions['order'] = "$t.sorting";
		}
	
		return static::findBy($arrColumns, $intPid, $arrOptions);
	}


	/**
	 * Find published content elements by their alias
	 *
	 * @param string $strAlias   The alias of the content element
	 * @param array  $arrOptions An optional options array
	 *
	 * @return \Model|\ElementsModel|null A model or null if there is no content element
	 */
	public function findPublishedByAlias($strAlias, array $arrOptions=array())
	{
		$t = static::$strTable;

		$arrColumns = array("$t.alias=? AND $t.invisible=''");
	
		return static::findOneBy($arrColumns, $strAlias, $arrOptions);
	}


	/**
	 * Find first published content element by pid (theme id)
	 *
	 * @param integer $intPid     The layout id
	 * @param array   $arrOptions An optional options array
	 *
	 * @return \Model|\ElementsModel|null A model or null if there is no content element
	 */
	public function findFirstPublishedElementByPid($intPid, array $arrOptions=array())
	{
		$t = static::$strTable;

		$arrColumns = array("$t.pid=? AND $t.invisible='' AND $t.type='element'");
	
		if (!isset($arrOptions['order']))
		{
			$arrOptions['order'] = "$t.sorting";
		}
	
		return static::findOneBy($arrColumns, $intPid, $arrOptions);
	}


	/**
	 * Find the default published content element by pid (theme id)
	 *
	 * @param integer $intPid     The layout id
	 * @param array   $arrOptions An optional options array
	 *
	 * @return \Model|\ElementsModel|null A model or null if there is no content element
	 */
	public function findDefaultPublishedElementByPid($intPid, array $arrOptions=array())
	{
		$t = static::$strTable;

		$arrColumns = array("$t.defaultType=1 AND $t.pid=? AND $t.invisible='' AND $t.type='element'");
	
		return static::findOneBy($arrColumns, $intPid, $arrOptions);
	}
}
