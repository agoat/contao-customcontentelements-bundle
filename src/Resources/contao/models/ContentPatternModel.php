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


/**
 * Reads and writes news
 *
 * @property integer $id
 * @property integer $pid
 * @property integer $tstamp
 * @property string  $headline
 * @property string  $alias
 * @property integer $author
 * @property integer $date
 * @property integer $time
 * @property string  $subheadline
 * @property string  $teaser
 * @property boolean $addImage
 * @property string  $singleSRC
 * @property string  $alt
 * @property string  $size
 * @property string  $imagemargin
 * @property string  $imageUrl
 * @property boolean $fullsize
 * @property string  $caption
 * @property string  $floating
 * @property boolean $addEnclosure
 * @property string  $enclosure
 * @property string  $source
 * @property integer $jumpTo
 * @property integer $articleId
 * @property string  $url
 * @property boolean $target
 * @property string  $cssClass
 * @property boolean $noComments
 * @property boolean $featured
 * @property boolean $published
 * @property string  $start
 * @property string  $stop
 * @property string  $authorName
 *
 * @method static $this findById($id, $opt=array())
 * @method static $this findByPk($id, $opt=array())
 * @method static $this findByIdOrAlias($val, $opt=array())
 * @method static $this findOneBy($col, $val, $opt=array())
 * @method static $this findOneByPid($val, $opt=array())
 * @method static $this findOneByTstamp($val, $opt=array())
 * @method static $this findOneByHeadline($val, $opt=array())
 * @method static $this findOneByAlias($val, $opt=array())
 * @method static $this findOneByAuthor($val, $opt=array())
 * @method static $this findOneByDate($val, $opt=array())
 * @method static $this findOneByTime($val, $opt=array())
 * @method static $this findOneBySubheadline($val, $opt=array())
 * @method static $this findOneByTeaser($val, $opt=array())
 * @method static $this findOneByAddImage($val, $opt=array())
 * @method static $this findOneBySingleSRC($val, $opt=array())
 * @method static $this findOneByAlt($val, $opt=array())
 * @method static $this findOneBySize($val, $opt=array())
 * @method static $this findOneByImagemargin($val, $opt=array())
 * @method static $this findOneByImageUrl($val, $opt=array())
 * @method static $this findOneByFullsize($val, $opt=array())
 * @method static $this findOneByCaption($val, $opt=array())
 * @method static $this findOneByFloating($val, $opt=array())
 * @method static $this findOneByAddEnclosure($val, $opt=array())
 * @method static $this findOneByEnclosure($val, $opt=array())
 * @method static $this findOneBySource($val, $opt=array())
 * @method static $this findOneByJumpTo($val, $opt=array())
 * @method static $this findOneByArticleId($val, $opt=array())
 * @method static $this findOneByUrl($val, $opt=array())
 * @method static $this findOneByTarget($val, $opt=array())
 * @method static $this findOneByCssClass($val, $opt=array())
 * @method static $this findOneByNoComments($val, $opt=array())
 * @method static $this findOneByFeatured($val, $opt=array())
 * @method static $this findOneByPublished($val, $opt=array())
 * @method static $this findOneByStart($val, $opt=array())
 * @method static $this findOneByStop($val, $opt=array())
 *
 * @method static integer countById($id, $opt=array())
 * @method static integer countByPid($val, $opt=array())
 * @method static integer countByTstamp($val, $opt=array())
 * @method static integer countByHeadline($val, $opt=array())
 * @method static integer countByAlias($val, $opt=array())
 * @method static integer countByAuthor($val, $opt=array())
 * @method static integer countByDate($val, $opt=array())
 * @method static integer countByTime($val, $opt=array())
 * @method static integer countBySubheadline($val, $opt=array())
 * @method static integer countByTeaser($val, $opt=array())
 * @method static integer countByAddImage($val, $opt=array())
 * @method static integer countBySingleSRC($val, $opt=array())
 * @method static integer countByAlt($val, $opt=array())
 * @method static integer countBySize($val, $opt=array())
 * @method static integer countByImagemargin($val, $opt=array())
 * @method static integer countByImageUrl($val, $opt=array())
 * @method static integer countByFullsize($val, $opt=array())
 * @method static integer countByCaption($val, $opt=array())
 * @method static integer countByFloating($val, $opt=array())
 * @method static integer countByAddEnclosure($val, $opt=array())
 * @method static integer countByEnclosure($val, $opt=array())
 * @method static integer countBySource($val, $opt=array())
 * @method static integer countByJumpTo($val, $opt=array())
 * @method static integer countByArticleId($val, $opt=array())
 * @method static integer countByUrl($val, $opt=array())
 * @method static integer countByTarget($val, $opt=array())
 * @method static integer countByCssClass($val, $opt=array())
 * @method static integer countByNoComments($val, $opt=array())
 * @method static integer countByFeatured($val, $opt=array())
 * @method static integer countByPublished($val, $opt=array())
 * @method static integer countByStart($val, $opt=array())
 * @method static integer countByStop($val, $opt=array())
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class ContentPatternModel extends \Model
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
				'order'		=>	"$t.rid ASC",
			),
			$arrOptions
		);

		return static::find($arrOptions);
	}



}
