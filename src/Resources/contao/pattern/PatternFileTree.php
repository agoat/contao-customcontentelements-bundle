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
use Contao\Config;
use Contao\Environment;
use Contao\File;
use Agoat\ContentBlocks\Pattern;


class PatternFileTree extends Pattern
{


	/**
	 * generate the DCA construct
	 */
	public function construct()
	{
		// set some options
		switch ($this->source)
		{
			case 'image':
				$extensions = Config::get('validImageTypes');
				$orderField = $this->virtualFieldName('orderSRC');
				$isGallery = true;
				$isDownloads = false;
				break;
				
			case 'video':
				$extensions = Config::get('validVideoTypes');
				$orderField = false;
				$isGallery = false;
				$isDownloads = false;
				break;
				
			case 'audio':
				$extensions = Config::get('validAudioTypes');
				$orderField = false;
				$isGallery = false;
				$isDownloads = false;
				break;
				
			case 'custom':
				$extensions = $this->customExtension;
				$orderField = $this->virtualFieldName('orderSRC');
				$isGallery = false;
				$isDownloads = true;
				break;
				
			default:
				$extensions = Config::get('allowedDownload');
				$orderField = $this->virtualFieldName('orderSRC');
				$isGallery = false;
				$isDownloads = true;
				break;				
		}
		
		if ($this->multiSource)
		{
			// the multiSRC field
			$this->generateDCA('multiSRC', array
			(
				'inputType' =>	'fileTree',
				'label'		=>	array($this->label, $this->description),
				'eval'     	=> 	array
				(
					'multiple'		=>	true,				
					'fieldType'		=>	'checkbox', 
					'orderField'	=>	$orderField,
					'files'			=>	true,
					'extensions' 	=>	$extensions,
					'isGallery'		=>	$isGallery,
					'isDownloads'	=>	$isDownloads,
					'mandatory'		=>	($this->mandatory) ? true : false, 
					'tl_class'		=>	'clr',
				),
				'load_callback'		=> (!$orderField) ?: array
				(
					array('tl_content_contentblocks', 'prepareOrderSRCValue'),
				),
				'save_callback'		=> (!$orderField) ?: array
				(
					array('tl_content_contentblocks', 'saveOrderSRCValue'),
				),
			));
			
		}
		else
		{
			// the singleSRC field
			$this->generateDCA('singleSRC', array
			(
				'inputType' =>	'fileTree',
				'label'		=>	array($this->label, $this->description),
				'eval'     	=> 	array
				(
					'filesOnly'		=>	true, 
					'fieldType'		=>	'radio', 
					'extensions' 	=>	$extensions,
					'mandatory'		=>	($this->mandatory) ? true : false, 
					'tl_class'		=>	'clr',
				),
			));
			
		}
		
		// the size field
		if ($this->source == 'image' && $this->canChangeSize)
		{
			$this->generateDCA('size', array
			(
				'label'				=> &$GLOBALS['TL_LANG']['tl_content']['size'],
				'inputType'			=> 'imageSize',
				'options'			=> $this->getImageSizeList(),
				'default'			=> $this->size,
				'reference'			=> &$GLOBALS['TL_LANG']['MSC'],
				'eval' =>	array
				(
					'rgxp' =>' natural', 
					'includeBlankOption' => false, 
					'nospace' => true, 
					'helpwizard' => true, 
					'tl_class' => 'w50 clr',
				),
				'load_callback'	=>	array
				(
					array('tl_content_contentblocks','defaultValue')
				),
			));	
		}

		// the sortBy field
		if ($this->multiSource && $this->canChangeSortBy)
		{
			$this->generateDCA('sortBy', array
			(
				'inputType'               => 'select',
				'label'                   => &$GLOBALS['TL_LANG']['tl_content']['sortBy'],
				'options'                 => array('custom', 'name_asc', 'name_desc', 'date_asc', 'date_desc', 'random'),
				'reference'               => &$GLOBALS['TL_LANG']['tl_content'],
				'eval'                    => array
				(
					'tl_class'=>'w50'
				),
			));		
		}
		
	}
	

	/**
	 * Generate backend output
	 */
	public function view()
	{
		$strPreview = '<div class="inline" style="padding-top:10px;"><h3 style="margin: 0;"><label>' . $this->label . '</label></h3><div class="selector_container"><ul class="' . (($this->source == 'image') ? 'sgallery' : '') . '">';

		switch ($this->source)
		{
			case 'image':
				if ($this->multiSource)
				{
					$strPreview .= '<li><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mPY/B8AAmgBsyYNIzsAAAAASUVORK5CYII=" width="80" height="60" alt="" class="gimage"></li><li><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mPY/B8AAmgBsyYNIzsAAAAASUVORK5CYII=" width="80" height="60" alt="" class="gimage"></li><li><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mPY/B8AAmgBsyYNIzsAAAAASUVORK5CYII=" width="80" height="60" alt="" class="gimage"></li>';				
				}
				else
				{
					$strPreview .= '<li><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mPY/B8AAmgBsyYNIzsAAAAASUVORK5CYII=" width="80" height="60" alt="" class="gimage"></li>';
				}
				break;
				
			case 'video':
				$strPreview .= '<li><img src="assets/contao/images/iconVIDEO.svg" width="18" height="18" alt=""> <span class="dirname">files/folder/</span>videofile.mp4 <span class="tl_gray">(5.0 MiB)</span></li><li><img src="assets/contao/images/iconVIDEO.svg" width="18" height="18" alt=""> <span class="dirname">files/folder/</span>videofile.webm <span class="tl_gray">(4.9 MiB)</span></li><li><img src="assets/contao/images/iconVIDEO.svg" width="18" height="18" alt=""> <span class="dirname">files/folder/</span>videofile.ogv <span class="tl_gray">(5.4 MiB)</span></li>';				
				break;
				
			case 'audio':
				$strPreview .= '<li><img src="assets/contao/images/iconAUDIO.svg" width="18" height="18" alt=""> <span class="dirname">files/folder/</span>audiofile.mp3 <span class="tl_gray">(2.5 MiB)</span></li><li><img src="assets/contao/images/iconAUDIO.svg" width="18" height="18" alt=""> <span class="dirname">files/folder/</span>audiofile.wma <span class="tl_gray">(3.4 MiB)</span></li>';				
				break;
				
			case 'custom':
			default:
				if ($this->multiSource)
				{
					$strPreview .= '<li><img src="assets/contao/images/iconPLAIN.svg" width="18" height="18" alt=""> <span class="dirname">files/folder/</span>selectedfile1.ext <span class="tl_gray">(128.0 KiB)</span></li><li><img src="assets/contao/images/iconPLAIN.svg" width="18" height="18" alt=""> <span class="dirname">files/folder/</span>selectedfile2.ext <span class="tl_gray">(2.0 MiB)</span></li><li><img src="assets/contao/images/iconPLAIN.svg" width="18" height="18" alt=""> <span class="dirname">files/folder/</span>selectedfile3.ext <span class="tl_gray">(512.0 KiB)</span></li>';				
				}
				else
				{
					$strPreview .= '<li><img src="assets/contao/images/iconPLAIN.svg" width="18" height="18" alt=""> files/folder/selectedfile1.ext <span class="tl_gray">(2.5 MiB)</span></li>';				
				}
				break;				
		}
		
	
		$strPreview .= '</ul><p><a href="javascript:void(0);" class="tl_submit">Change selection</a></p></div><p title="" class="tl_help tl_tip">' . $this->description . '</p>';

		if ($this->canChangeSize)
		{
			$strPreview .= '<h3 style="margin: 0; padding-top: 14px;"><label>' . $GLOBALS['TL_LANG']['tl_content_pattern']['size'][0] . '</label></h3><div class="tl_image_size"><select class="tl_select_interval">';
	
			$imgSizes = $this->getImageSizeList();
			$size = deserialize($this->size);

			foreach ($imgSizes as $k=>$v)
			{
				if (!is_array($v))
				{
					$selected = ((is_numeric($size[2])) ? $kk == $size[2] : $vv == $size[2]) ? ' selected' : '';
					$label = ($GLOBALS['TL_LANG']['MSC'][$v]) ? (is_array($GLOBALS['TL_LANG']['MSC'][$v])) ? $GLOBALS['TL_LANG']['MSC'][$v][0] : $GLOBALS['TL_LANG']['MSC'][$v] : $v;
					$strPreview .= '<option value="' . specialchars($v) . '"' . $selected . '>' . $label . '</option>';
				}
				else
				{
					$label = ($GLOBALS['TL_LANG']['MSC'][$k]) ? (is_array($GLOBALS['TL_LANG']['MSC'][$k])) ? $GLOBALS['TL_LANG']['MSC'][$k][0] : $GLOBALS['TL_LANG']['MSC'][$k] : $k;
					$strPreview .= '<optgroup label="&nbsp;' . specialchars($label) . '">';
					
					$bolAssoc = array_is_assoc($v);
					foreach ($v as $kk=>$vv)
					{
						$selected = ((is_numeric($size[2]) && $bolAssoc) ? $kk == $size[2] : $vv == $size[2]) ? ' selected' : '';
						$strPreview .= '<option value="' . specialchars($vv) . '"' . $selected . '>' . (($GLOBALS['TL_LANG']['MSC'][$vv]) ? $GLOBALS['TL_LANG']['MSC'][$vv][0] : $vv) . '</option>';
					}

					$strPreview .= '</optgroup>';
				}
			}
	
		
			$strPreview .= '</select> <input class="tl_text_4 tl_imageSize_0" value="" type="text"> <input class="tl_text_4 tl_imageSize_1" value="" type="text"></div><p title="" class="tl_help tl_tip">' . $GLOBALS['TL_LANG']['tl_content_pattern']['size'][1] . '</p>';
		}
		
		$strPreview .= '</div>';	

		return $strPreview;
	}


	/**
	 * Generate data for the frontend template 
	 */
	public function compile()
	{
	
		if ($this->multiSource)
		{
			$multiSRC = deserialize($this->Value->multiSRC);
			
			// Return if there are no files
			if (!is_array($multiSRC) || empty($multiSRC))
			{
				return '';
			}
			
			$objFiles = \FilesModel::findMultipleByUuids($multiSRC);			
		}
		else
		{
			// Return if there is no file
			if ($this->Value->singleSRC == '')
			{
				return '';
			}
		
			$objFiles = \FilesModel::findMultipleByUuids(array($this->Value->singleSRC));
		}

		if ($objFiles === null)
		{
			if (!\Validator::isUuid($multiSRC[0]))
			{
				return '<p class="error">'.$GLOBALS['TL_LANG']['ERR']['version2format'].'</p>';
			}
			return '';
		}
		
		$file = \Input::get('file', true);
		
		// Send the file to the browser and do not send a 404 header (see #4632)
		if ($file != '' && !preg_match('/^meta(_[a-z]{2})?\.txt$/', basename($file)))
		{
			while ($objFiles->next())
			{
				if ($file == $objFiles->path || dirname($file) == $objFiles->path)
				{
					\Controller::sendFileToBrowser($file);
				}
			}
			$objFiles->reset();
		}
		
		global $objPage;
		$allowedDownload = trimsplit(',', strtolower(\Config::get('allowedDownload')));
		$allowedVideo = trimsplit(',', strtolower(\Config::get('validVideoTypes')));
		$allowedAudio = trimsplit(',', strtolower(\Config::get('validAudioTypes')));
		
		$files = array();
		$auxDate = array();
		

		// Get all files
		while ($objFiles->next())
		{
			// Continue if the files has been processed or does not exist
			if (isset($files[$objFiles->path]) || !file_exists(TL_ROOT . '/' . $objFiles->path))
			{
				continue;
			}
			
			// Single files
			if ($objFiles->type == 'file')
			{
				$objFile = new \File($objFiles->path, true);
				
				switch ($this->source)
				{
					case 'image':
						if (!$objFile->isImage)
						{
							continue 2;
						}
						break;
						
					case 'video':
						if (!in_array($objFile->extension, $allowedVideo))
						{
							continue 2;
						}
						break;
					
					case 'audio':
						if (!in_array($objFile->extension, $allowedAudio))
						{
							continue 2;
						}
						break;
					
					default:
						if ((!in_array($objFile->extension, $allowedDownload) || preg_match('/^meta(_[a-z]{2})?\.txt$/', $objFile->basename)))
						{
							continue 2;
						}
					break;
				}

				
				$arrMeta = \Frontend::getMetaData($objFiles->meta, $objPage->language);
				
				if (empty($arrMeta))
				{
					if ($this->metaIgnore)
					{
						continue;
					}
					elseif ($objPage->rootFallbackLanguage !== null)
					{
						$arrMeta = \Frontend::getMetaData($objFiles->meta, $objPage->rootFallbackLanguage);
					}
				}
				
				// Use the file name as title if none is given
				if ($arrMeta['title'] == '')
				{
					$arrMeta['title'] = specialchars($objFile->basename);
				}

				$strHref = Environment::get('request');
				
				// Remove an existing file parameter (see #5683)
				if (preg_match('/(&(amp;)?|\?)file=/', $strHref))
				{
					$strHref = preg_replace('/(&(amp;)?|\?)file=[^&]+/', '', $strHref);
				}
				
				$strHref .= ((\Config::get('disableAlias') || strpos($strHref, '?') !== false) ? '&amp;' : '?') . 'file=' . System::urlEncode($objFiles->path);
				
				
				// Add the file
				$files[$objFiles->path] = array
				(
					'id'        => $objFiles->id,
					'uuid'      => $objFiles->uuid,
					'name'      => $objFile->basename,
					'path' 		=> $objFiles->path,
					'size'		=> ($this->canChangeSize) ? $this->Value->size : $this->size,
					'alt'       => $arrMeta['title'],
					'title'     => $arrMeta['title'],
					'imageUrl'  => $arrMeta['link'],
					'caption'   => $arrMeta['caption'],
					'href'      => $strHref,
					'filesize'  => $this->getReadableSize($objFile->filesize, 1),
					'icon'      => TL_ASSETS_URL . 'assets/contao/images/' . $objFile->icon,
					'mime'      => $objFile->mime,
					'extension' => $objFile->extension
				);
				
				$auxDate[] = $objFile->mtime;
			}
			
			// Folders
			else
			{
				$objSubfiles = \FilesModel::findByPid($objFiles->uuid);
				
				if ($objSubfiles === null)
				{
					continue;
				}
				
				while ($objSubfiles->next())
				{
					// Skip subfolders
					if ($objSubfiles->type == 'folder')
					{
						continue;
					}
				
					$objFile = new \File($objSubfiles->path, true);
					
					switch ($this->source)
					{
						case 'image':
							if (!$objFile->isImage)
							{
								continue 2;
							}
							break;
						
						case 'video':
							if (!in_array($objFile->extension, $allowedVideo))
							{
								continue 2;
							}
							break;
						
						case 'audio':
							if (!in_array($objFile->extension, $allowedAudio))
							{
								continue 2;
							}
							break;
					
						default:
							if ((!in_array($objFile->extension, $allowedDownload) || preg_match('/^meta(_[a-z]{2})?\.txt$/', $objFile->basename)))
							{
								continue 2;
							}
						break;
					}
					
					$arrMeta = \Frontend::getMetaData($objSubfiles->meta, $objPage->language);
					
					if (empty($arrMeta))
					{
						if ($this->metaIgnore)
						{
							continue;
						}
						elseif ($objPage->rootFallbackLanguage !== null)
						{
							$arrMeta = \Frontend::getMetaData($objSubfiles->meta, $objPage->rootFallbackLanguage);
						}
					}
					
					// Use the file name as title if none is given
					if ($arrMeta['title'] == '')
					{
						$arrMeta['title'] = specialchars($objFile->basename);
					}

					$strHref = \Environment::get('request');
					
					// Remove an existing file parameter (see #5683)
					if (preg_match('/(&(amp;)?|\?)file=/', $strHref))
					{
						$strHref = preg_replace('/(&(amp;)?|\?)file=[^&]+/', '', $strHref);
					}
					
					$strHref .= ((\Config::get('disableAlias') || strpos($strHref, '?') !== false) ? '&amp;' : '?') . 'file=' . \System::urlEncode($objSubfiles->path);

					
					// Add the image
					$files[$objSubfiles->path] = array
					(
						'id'        => $objFiles->id,
						'uuid'      => $objFiles->uuid,
						'name'      => $objFile->basename,
						'path' 		=> $objFiles->path . '/' . $objFile->basename,
						'size'		=> ($this->canChangeSize) ? $this->Value->size : $this->size,
						'alt'       => $arrMeta['title'],
						'title'     => $arrMeta['title'],
						'imageUrl'  => $arrMeta['link'],
						'caption'   => $arrMeta['caption'],
						'href'      => $strHref,
						'filesize'  => $this->getReadableSize($objFile->filesize, 1),
						'icon'      => TL_ASSETS_URL . 'assets/contao/images/' . $objFile->icon,
						'mime'      => $objFile->mime,
						'extension' => $objFile->extension
					);
					
					$auxDate[] = $objFile->mtime;
				}
			}
		}
		
		// Sort array
		switch (($this->canChangeSortBy) ? $this->Value->sortBy : $this->sortBy)
		{
			default:
			case 'name_asc':
				uksort($files, 'basename_natcasecmp');
				break;
			
			case 'name_desc':
				uksort($files, 'basename_natcasercmp');
				break;
			
			case 'date_asc':
				array_multisort($files, SORT_NUMERIC, $auxDate, SORT_ASC);
				break;
			
			case 'date_desc':
				array_multisort($files, SORT_NUMERIC, $auxDate, SORT_DESC);
				break;
			
			case 'custom':
				if ($this->Value->orderSRC != '')
				{
					$tmp = deserialize($this->Value->orderSRC);
					
					if (!empty($tmp) && is_array($tmp))
					{
						// Remove all values
						$arrOrder = array_map(function(){}, array_flip($tmp));
						
						// Move the matching elements to their position in $arrOrder
						foreach ($files as $k=>$v)
						{
							if (array_key_exists($v['uuid'], $arrOrder))
							{
								$arrOrder[$v['uuid']] = $v;
								unset($files[$k]);
							}
						}
						
						// Append the left-over files at the end
						if (!empty($files))
						{
							$arrOrder = array_merge($arrOrder, array_values($files));
						}
						
						// Remove empty (unreplaced) entries
						$files = array_values(array_filter($arrOrder));
						
						unset($arrOrder);
					}
				}
				break;
			
			case 'random':
				shuffle($files);
				break;

			case 'html5media':
				// Pre-sorted array for recommended html4 video and audio order
				$arrOrder = array('mp4'=>null, 'm4v'=>null, 'mov'=>null, 'wmv'=>null, 'webm'=>null, 'ogv'=>null, 'm4a'=>null, 'mp3'=>null, 'wma'=>null, 'mpeg'=>null, 'wav'=>null, 'ogg'=>null);
				
				foreach ($files as $file)
				{
					if (!is_array($arrOrder[$file['extension']]))
					{
						$arrOrder[$file['extension']] = $file;
					}
					
				}

				// Remove empty (unreplaced) entries
				$files = array_values(array_filter($arrOrder));

				break;
		}

		$files = array_values($files);
		
		// Limit the total number of items
		if ($this->numberOfItems > 0)
		{
			$files = array_slice($files, 0, $this->numberOfItems);
		}
		
		
		// prepare images
		if ($this->source == 'image')
		{
			$pictures = array();
			foreach ($files as $key=>$image)
			{
				$image['singleSRC'] = $image['path'];
				
				$this->addImageToTemplate($pictures[$key] = new \stdClass(), $image);	
				
				$pictures[$key] = $pictures[$key]->picture;
				$pictures[$key]['imageUrl'] = $image['imageUrl'];
				$pictures[$key]['caption'] = $image['caption'];
				$pictures[$key]['id'] = $image['id'];
				$pictures[$key]['uuid'] = $image['uuid'];
				$pictures[$key]['name'] = $image['name'];
				$pictures[$key]['path'] = $image['path'];
				$pictures[$key]['extension'] = $image['extension'];
			}
			
			$files = $pictures;
		}
		
		// no collection array for single source
		if (!$this->multiSource)
		{
			$files = $files[0];
		}

		$this->writeToTemplate($files);

	}


	
	
	
	/**
	 * get a list of image sizes
	 */
	public function getImageSizeList()
	{
		$arrSizes = System::getImageSizes();
		$arrSizes['image_sizes'] = array_intersect_key($arrSizes['image_sizes'], array_flip(deserialize($this->sizeList)));
		
		if ($this->canEnterSize)
		{
			return $arrSizes;
		}
		return $arrSizes['image_sizes'];
	}


	
}
