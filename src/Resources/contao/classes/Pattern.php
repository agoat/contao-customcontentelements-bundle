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

 
namespace Agoat\ContentBlocks;
 
use Agoat\ContentBlocks\Controller;
use \Contao\FilesModel;

/**
 * Pattern class
 *
 * @property integer $id
 * @property integer $pid


 *
 * @author Arne Stappen
 */
abstract class Pattern extends Controller
{

	/**
	 * Model
	 * @var \ContentElement
	 */
	protected $objPattern;
	
	/**
	 * Processed folders
	 * @var array
	 */
	protected $arrData = array();

	protected $arrMapper = false;
	
	/**
	 * Initialize the object
	 *
	 * @param \PatternModel $objPattern
	*/
	public function __construct($objPattern)
	{
		$this->arrData = $objPattern->row();
	} 

	/**
	 * Set an object property
	 *
	 * @param string $strKey
	 * @param mixed  $varValue
	 */
	public function __set($strKey, $varValue)
	{
		$this->arrData[$strKey] = $varValue;
	}


	/**
	 * Return an object property
	 *
	 * @param string $strKey
	 *
	 * @return mixed
	 */
	public function __get($strKey)
	{
		if (isset($this->arrData[$strKey]))
		{
			return $this->arrData[$strKey];
		}

		return parent::__get($strKey);
	}


	/**
	 * Check whether a property is set
	 *
	 * @param string $strKey
	 *
	 * @return boolean
	 */
	public function __isset($strKey)
	{
		return isset($this->arrData[$strKey]);
	}

	
	/**
	 * Return the current record as associative array
	 *
	 * @return array The data record
	 */
	public function row()
	{
		return $this->arrData;
	}	

	/**
	 * construct the DCA array
	 *
	 */
	public function generateDCA($strFieldName, $arrFieldDCA=array(), $bolLoadSaveCallback=true)
	{
		$this->virtualFieldAlias = $this->virtualFieldName($strFieldName);
		
		// add some standard field attributes
		$arrFieldDCA['eval']['doNotSaveEmpty'] = true;
		
		if ($bolLoadSaveCallback)
		{
			$arrFieldDCA['load_callback'] = is_array($arrFieldDCA['load_callback']) ? $arrFieldDCA['load_callback'] : array();
			$arrFieldDCA['save_callback'] = is_array($arrFieldDCA['save_callback']) ? $arrFieldDCA['save_callback'] : array();
			
			// load default value
			if ($arrFieldDCA['default'])
			{
				array_unshift($arrFieldDCA['load_callback'], array('tl_content_contentblocks', 'defaultValue'));
			}

			// load/save database values first/last
			array_unshift($arrFieldDCA['load_callback'], array('tl_content_contentblocks', 'loadFieldValue'));
			array_push($arrFieldDCA['save_callback'], array('tl_content_contentblocks', 'saveFieldAndClear'));
		}

		$GLOBALS['TL_DCA']['tl_content']['palettes'][$this->alias] .= ','.$this->virtualFieldAlias;		

		// add field informations
		$GLOBALS['TL_DCA']['tl_content']['fields'][$this->virtualFieldAlias] = $arrFieldDCA;

	}

	
	/**
	 * prepare a field view for the backend
	 *
	 */
	abstract public function view();

	
	/**
	 * push the values to the template object
	 *
	 */
	public function writeToTemplate($Value)
	{

		if (is_array($this->arrMapper))
		{
		
			$arrValue[$this->arrMapper[0]] = $this->Template->{$this->arrMapper[0]};
			
			$map =& $arrValue;
			
			foreach ($this->arrMapper as $key)
			{
				if (!is_array($map[$key]))
				{
					$map[$key] = array();
				}
				
				$map =& $map[$key];
			}
			
			$map[$this->alias] = $Value;

			$this->Template->{$this->arrMapper[0]} = $arrValue[$this->arrMapper[0]];
			return;
		}	
	
		$this->Template->{$this->alias} = $Value;
	}


	
	
	/**
	 * generate a field alias with the right syntax
	 *
	 * @param string $strName The field name (tl_content_value column name)
	 *
	 * @return string The field alias
	 */
	protected function virtualFieldName($strName)
	{
		if (!$this->rid)
		{
			$this->rid = 0;
		}
	
		// field alias syntax: tablecolumn_patternId_recursiveId
		return $strName.'-'.$this->id.'-'.$this->rid;
	}

	



	/**
	 * Find a content element in the TL_CTE array and return the class name
	 *
	 * @param string $strName The content element name
	 *
	 * @return string The class name
	 */
	public static function findClass($strName)
	{
		foreach ($GLOBALS['TL_CTP'] as $pp)
		{
			foreach ($pp as $kk=>$vv)
			{
				if ($kk == $strName)
				{
					return $vv;
				}
			}
		}
		
		return '';
	}

	
	/**
	 * Add an image to a template (New method for pattern without using the max backend width of 320px)
	 *
	 * @param object     $objTemplate   The template object to add the image to
	 * @param array      $arrItem       The element or module as array
	 * @param integer    $intMaxWidth   An optional maximum width of the image
	 * @param string     $strLightboxId An optional lightbox ID
	 * @param FilesModel $objModel      An optional files model
	 */
	public static function addImageToTemplate($objTemplate, $arrItem, $intMaxWidth=null, $strLightboxId=null, FilesModel $objModel=null)
	{
		try
		{
			$objFile = new \File($arrItem['singleSRC']);
		}
		catch (\Exception $e)
		{
			$objFile = new \stdClass();
			$objFile->imageSize = false;
		}

		$imgSize = $objFile->imageSize;
		$size = \StringUtil::deserialize($arrItem['size']);

		if (is_numeric($size))
		{
			$size = array(0, 0, (int) $size);
		}
		elseif (!is_array($size))
		{
			$size = array();
		}

		$size += array(0, 0, 'crop');

		if ($intMaxWidth === null)
		{
			$intMaxWidth = \Config::get('maxImageWidth');
		}

		$arrMargin = \StringUtil::deserialize($arrItem['imagemargin']);

		// Store the original dimensions
		$objTemplate->width = $imgSize[0];
		$objTemplate->height = $imgSize[1];

		// Adjust the image size
		if ($intMaxWidth > 0)
		{
			// Subtract the margins before deciding whether to resize (see #6018)
			if (is_array($arrMargin) && $arrMargin['unit'] == 'px')
			{
				$intMargin = (int) $arrMargin['left'] + (int) $arrMargin['right'];

				// Reset the margin if it exceeds the maximum width (see #7245)
				if ($intMaxWidth - $intMargin < 1)
				{
					$arrMargin['left'] = '';
					$arrMargin['right'] = '';
				}
				else
				{
					$intMaxWidth = $intMaxWidth - $intMargin;
				}
			}

			if ($size[0] > $intMaxWidth || (!$size[0] && !$size[1] && (!$imgSize[0] || $imgSize[0] > $intMaxWidth)))
			{
				// See #2268 (thanks to Thyon)
				$ratio = ($size[0] && $size[1]) ? $size[1] / $size[0] : (($imgSize[0] && $imgSize[1]) ? $imgSize[1] / $imgSize[0] : 0);

				$size[0] = $intMaxWidth;
				$size[1] = floor($intMaxWidth * $ratio);
			}
		}


        try
		{
			$src = \System::getContainer()->get('contao.image.image_factory')->create(TL_ROOT . '/' . $arrItem['singleSRC'], $size)->getUrl(TL_ROOT);
			$picture = \System::getContainer()->get('contao.image.picture_factory')->create(TL_ROOT . '/' . $arrItem['singleSRC'], $size);

			$picture = array
			(
				'img' => $picture->getImg(TL_ROOT, TL_FILES_URL),
				'sources' => $picture->getSources(TL_ROOT, TL_FILES_URL)
			);

			if ($src !== $arrItem['singleSRC'])
			{
				$objFile = new \File(rawurldecode($src));
			}
		}
		catch (\Exception $e)
		{
			\System::log('Image "' . $arrItem['singleSRC'] . '" could not be processed: ' . $e->getMessage(), __METHOD__, TL_ERROR);

			$src = '';
			$picture = array('img'=>array('src'=>'', 'srcset'=>''), 'sources'=>array());
		}

		// Image dimensions
		if (($imgSize = $objFile->imageSize) !== false)
		{
			$objTemplate->arrSize = $imgSize;
			$objTemplate->imgSize = ' width="' . $imgSize[0] . '" height="' . $imgSize[1] . '"';
		}

		$arrMeta = array();

		// Load the meta data
		if ($objModel instanceof FilesModel)
		{
			if (TL_MODE == 'FE')
			{
				global $objPage;

				$arrMeta = \Frontend::getMetaData($objModel->meta, $objPage->language);

				if (empty($arrMeta) && $objPage->rootFallbackLanguage !== null)
				{
					$arrMeta = \Frontend::getMetaData($objModel->meta, $objPage->rootFallbackLanguage);
				}
			}
			else
			{
				$arrMeta = \Frontend::getMetaData($objModel->meta, $GLOBALS['TL_LANGUAGE']);
			}

			\Controller::loadDataContainer('tl_files');

			// Add any missing fields
			foreach (array_keys($GLOBALS['TL_DCA']['tl_files']['fields']['meta']['eval']['metaFields']) as $k)
			{
				if (!isset($arrMeta[$k]))
				{
					$arrMeta[$k] = '';
				}
			}

			$arrMeta['imageTitle'] = $arrMeta['title'];
			$arrMeta['imageUrl'] = $arrMeta['link'];
			unset($arrMeta['title'], $arrMeta['link']);

			// Add the meta data to the item
			if (!$arrItem['overwriteMeta'])
			{
				foreach ($arrMeta as $k=>$v)
				{
					switch ($k)
					{
						case 'alt':
						case 'imageTitle':
							$arrItem[$k] = \StringUtil::specialchars($v);
							break;

						default:
							$arrItem[$k] = $v;
							break;
					}
				}
			}
		}

		$picture['alt'] = \StringUtil::specialchars($arrItem['alt']);

		// Move the title to the link tag so it is shown in the lightbox
		if ($arrItem['fullsize'] && $arrItem['imageTitle'] && !$arrItem['linkTitle'])
		{
			$arrItem['linkTitle'] = $arrItem['imageTitle'];
			unset($arrItem['imageTitle']);
		}

		if (isset($arrItem['imageTitle']))
		{
			$picture['title'] = \StringUtil::specialchars($arrItem['imageTitle']);
		}

		$objTemplate->picture = $picture;

		// Provide an ID for single lightbox images in HTML5 (see #3742)
		if ($strLightboxId === null && $arrItem['fullsize'])
		{
			$strLightboxId = 'lightbox[' . substr(md5($objTemplate->getName() . '_' . $arrItem['id']), 0, 6) . ']';
		}

		// Float image
		if ($arrItem['floating'])
		{
			$objTemplate->floatClass = ' float_' . $arrItem['floating'];
		}

		// Do not override the "href" key (see #6468)
		$strHrefKey = ($objTemplate->href != '') ? 'imageHref' : 'href';

		// Image link
		if ($arrItem['imageUrl'] && TL_MODE == 'FE')
		{
			$objTemplate->$strHrefKey = $arrItem['imageUrl'];
			$objTemplate->attributes = '';

			if ($arrItem['fullsize'])
			{
				// Open images in the lightbox
				if (preg_match('/\.(jpe?g|gif|png)$/', $arrItem['imageUrl']))
				{
					// Do not add the TL_FILES_URL to external URLs (see #4923)
					if (strncmp($arrItem['imageUrl'], 'http://', 7) !== 0 && strncmp($arrItem['imageUrl'], 'https://', 8) !== 0)
					{
						$objTemplate->$strHrefKey = TL_FILES_URL . \System::urlEncode($arrItem['imageUrl']);
					}

					$objTemplate->attributes = ' data-lightbox="' . substr($strLightboxId, 9, -1) . '"';
				}
				else
				{
					$objTemplate->attributes = ' target="_blank"';
				}
			}
		}

		// Fullsize view
		elseif ($arrItem['fullsize'] && TL_MODE == 'FE')
		{
			$objTemplate->$strHrefKey = TL_FILES_URL . \System::urlEncode($arrItem['singleSRC']);
			$objTemplate->attributes = ' data-lightbox="' . substr($strLightboxId, 9, -1) . '"';
		}

		// Add the meta data to the template
		foreach (array_keys($arrMeta) as $k)
		{
			$objTemplate->$k = $arrItem[$k];
		}

		// Do not urlEncode() here because getImage() already does (see #3817)
		$objTemplate->src = TL_FILES_URL . $src;
		$objTemplate->singleSRC = $arrItem['singleSRC'];
		$objTemplate->linkTitle = $arrItem['linkTitle'] ?: $arrItem['title'];
		$objTemplate->fullsize = $arrItem['fullsize'] ? true : false;
		$objTemplate->addBefore = ($arrItem['floating'] != 'below');
		$objTemplate->margin = static::generateMargin($arrMargin);
		$objTemplate->addImage = true;
	}
}
