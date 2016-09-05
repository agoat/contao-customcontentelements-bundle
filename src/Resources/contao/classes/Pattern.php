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
 
use Contao\System;
use Contao\File;
use Agoat\ContentBlocks\Controller;

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


	
	/**
	 * Initialize the object
	 *
	 * @param \PatternModel $objPattern
	 * @param \ElementModel $objElement
	 * @param \SectionModel $objSection
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
		$strVirtualField = $this->virtualFieldName($strFieldName);
		
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

		// add to palette
		$GLOBALS['TL_DCA']['tl_content']['palettes'][$this->alias] .= ','.$strVirtualField;		

		// add field informations
		$GLOBALS['TL_DCA']['tl_content']['fields'][$strVirtualField] = $arrFieldDCA;


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

		if ($this->replicaAlias)
		{
			$replica = (is_array($this->Template->{$this->replicaAlias})) ? $this->Template->{$this->replicaAlias} : array();
		
			if (!is_object($replica[$this->replica]))
			{
				$replica[$this->replica] = new \stdClass();
			}
			
			$replica[$this->replica]->{$this->alias} = $Value;

			$this->Template->{$this->replicaAlias} = $replica;
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
		if (!$this->replica)
		{
			$this->replica = 0;
		}
	
		// field alias syntax: tablecolumn_patternId_multipartId
		return $strName.'_'.$this->id.'_'.$this->replica;
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
	 * Add an image to a template (new method for pattern)
	 *
	 * @param object  $objTemplate   The template object to add the image to
	 * @param array   $arrItem       The element or module as array
	 * @param integer $intMaxWidth   An optional maximum width of the image
	 * @param string  $strLightboxId An optional lightbox ID
	 */
	public static function addImageToTemplate($objTemplate, $arrItem, $intMaxWidth=null, $strLightboxId=null)
	{
		/** @var \PageModel $objPage */
		global $objPage;


		try
		{
			$objFile = new File($arrItem['singleSRC'], true);
		}
		catch (\Exception $e)
		{
			$objFile = new \stdClass();
			$objFile->imageSize = false;
		}

		$imgSize = $objFile->imageSize;

		// Store the original dimensions
		if ($imgSize !== false)
		{
			$objTemplate->width = $imgSize[0];
			$objTemplate->height = $imgSize[1];
		}

		$size = deserialize($arrItem['size']);
		$arrMargin = (TL_MODE == 'BE') ? array() : deserialize($arrItem['imagemargin']);

		if (is_array($size))
		{
			if ($intMaxWidth === null)
			{
				$intMaxWidth = \Config::get('maxImageWidth');
			}

			// Adjust the image size
			if ($intMaxWidth > 0 && $imgSize !== false)
			{
				// Subtract the margins before deciding whether to resize (see #6018)
				if (is_array($arrMargin) && $arrMargin['unit'] == 'px')
				{
					$intMargin = $arrMargin['left'] + $arrMargin['right'];

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

				if ($size[0] > $intMaxWidth || (!$size[0] && !$size[1] && $imgSize[0] > $intMaxWidth))
				{
					// See #2268 (thanks to Thyon)
					$ratio = ($size[0] && $size[1]) ? $size[1] / $size[0] : $imgSize[1] / $imgSize[0];

					$size[0] = $intMaxWidth;
					$size[1] = floor($intMaxWidth * $ratio);
				}
			}

		}

		try
		{
			$src = \Image::create($arrItem['singleSRC'], $size)->executeResize()->getResizedPath();
			$picture = \Picture::create($arrItem['singleSRC'], $size)->getTemplateData();

			if ($src !== $arrItem['singleSRC'])
			{
				$objFile = new File(rawurldecode($src), true);
			}
		}
		catch (\Exception $e)
		{
			System::log('Image "' . $arrItem['singleSRC'] . '" could not be processed: ' . $e->getMessage(), __METHOD__, TL_ERROR);

			$src = '';
			$picture = array('img'=>array('src'=>'', 'srcset'=>''), 'sources'=>array());
		}

		// Image dimensions
		if (($imgSize = $objFile->imageSize) !== false)
		{
			$objTemplate->arrSize = $imgSize;
			$objTemplate->imgSize = ' width="' . $imgSize[0] . '" height="' . $imgSize[1] . '"';
		}

		$picture['alt'] = specialchars($arrItem['alt']);
		$picture['title'] = specialchars($arrItem['title']);

		$objTemplate->picture = $picture;

		// Provide an ID for single lightbox images in HTML5 (see #3742)
		if ($strLightboxId === null && $arrItem['fullsize'])
		{
			if ($objPage->outputFormat == 'xhtml')
			{
				$strLightboxId = 'lightbox';
			}
			else
			{
				$strLightboxId = 'lightbox[' . substr(md5($objTemplate->getName() . '_' . $arrItem['id']), 0, 6) . ']';
			}
		}

		// Float image
		if ($arrItem['floating'] != '')
		{
			$objTemplate->floatClass = ' float_' . $arrItem['floating'];
		}

		// Do not override the "href" key (see #6468)
		$strHrefKey = ($objTemplate->href != '') ? 'imageHref' : 'href';

		// Image link
		if ($arrItem['imageUrl'] != '' && TL_MODE == 'FE')
		{
			$objTemplate->$strHrefKey = $arrItem['imageUrl'];
			$objTemplate->attributes = '';

			if ($arrItem['fullsize'])
			{
				// Open images in  he lightbox
				if (preg_match('/\.(jpe?g|gif|png)$/', $arrItem['imageUrl']))
				{
					// Do not add the TL_FILES_URL to external URLs (see #4923)
					if (strncmp($arrItem['imageUrl'], 'http://', 7) !== 0 && strncmp($arrItem['imageUrl'], 'https://', 8) !== 0)
					{
						$objTemplate->$strHrefKey = TL_FILES_URL . System::urlEncode($arrItem['imageUrl']);
					}

					$objTemplate->attributes = ($objPage->outputFormat == 'xhtml') ? ' rel="' . $strLightboxId . '"' : ' data-lightbox="' . substr($strLightboxId, 9, -1) . '"';
				}
				else
				{
					$objTemplate->attributes = ($objPage->outputFormat == 'xhtml') ? ' onclick="return !window.open(this.href)"' : ' target="_blank"';
				}
			}
		}

		// Fullsize view
		elseif ($arrItem['fullsize'] && TL_MODE == 'FE')
		{
			$objTemplate->$strHrefKey = TL_FILES_URL . System::urlEncode($arrItem['singleSRC']);
			$objTemplate->attributes = ($objPage->outputFormat == 'xhtml') ? ' rel="' . $strLightboxId . '"' : ' data-lightbox="' . substr($strLightboxId, 9, -1) . '"';
		}

		// Do not urlEncode() here because getImage() already does (see #3817)
		$objTemplate->src = TL_FILES_URL . $src;
		$objTemplate->alt = specialchars($arrItem['alt']);
		$objTemplate->title = specialchars($arrItem['title']);
		$objTemplate->linkTitle = $objTemplate->title;
		$objTemplate->fullsize = $arrItem['fullsize'] ? true : false;
		$objTemplate->addBefore = ($arrItem['floating'] != 'below');
		$objTemplate->margin = static::generateMargin($arrMargin);
		$objTemplate->caption = $arrItem['caption'];
		$objTemplate->singleSRC = $arrItem['singleSRC'];
		$objTemplate->addImage = true;
	}


}
