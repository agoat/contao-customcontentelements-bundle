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

use Contao\FrontendTemplate;


class Template extends FrontendTemplate
{
	
	/**
	 * Add Image to template
	 *
	 * @param object           $image The element or module as array
	 * @param object|\Model    $imageSize The image size or image size item model
	 * @param integer $width   An optional width of the image
	 * @param integer $height  An optional height of the image
	 *
	 * @return object  new image
	 */
	public function addImage ($image, $mode, $width=0, $height=0)
	{
		if (!is_array($image))
		{
			return;
		}
		
		if (is_numeric($mode))
		{
			$size = (int) $mode;
		}
		else
		{
			$size = array($width, $height, $mode);
		}
		
		$image['size'] = $size;
		$image['singleSRC'] = $image['path'];
		
		$this->addImageToTemplate($picture = new \stdClass(), $image);	
		
		$picture = $picture->picture;
		$picture['imageUrl'] = $image['imageUrl'];
		$picture['caption'] = $image['caption'];
		$picture['id'] = $image['id'];
		$picture['uuid'] = $image['uuid'];
		$picture['name'] = $image['name'];
		$picture['path'] = $image['path'];
		$picture['extension'] = $image['extension'];

		// Return new image object
		return $picture;
	}

	
	/**
	 * Add CSS to template
	 *
	 * @param string   The css, scss or less content
	 * @param string   The type of the content
	 * @param boolean  If false, add the content in a extra file (only when type is css)
	 */
	public function addCSS ($strCSS, $strType='scss', $bolStatic=true)
	{
		if ($strCSS == '')
		{
			return;
		}
		
		if (!in_array($strType, array('css', 'scss' , 'less')))
		{
			return;
		}
		
		if (!$bolStatic && $strType == 'css')
		{
			$strKey = substr(md5($strType . $strCSS), 0, 12);
			$strPath = 'assets/css/' . $strKey . '.' . $strType;
			
			// Write to a temporary file in the assets folder
			if (!file_exists($strPath))
			{
				$objFile = new \File($strPath, true);
				$objFile->write($strCSS);
				$objFile->close();
			}
			
			// add file path to TL_USER_CSS
			$GLOBALS[TL_USER_CSS][] = $strPath;
		
			return;
		}
		// add to combined CSS string
		$GLOBALS['TL_CTB_' . $strType] .= $strCSS;
	}

	
	/**
	 * Add JS to template
	 *
	 * @param string   The javascript content
	 * @param boolean  If false, add the content in a extra file
	 */
	public function addJS ($strJS, $bolStatic=true)
	{
		if ($strJS == '')
		{
			return;
		}
		
		if (!$bolStatic)
		{
			$strKey = substr(md5('js' . $strJS), 0, 12);
			$strPath = 'assets/js/' . $strKey . '.js';
			
			// Write to a temporary file in the assets folder
			if (!file_exists($strPath))
			{
				$objFile = new \File($strPath, true);
				$objFile->write($strCSS);
				$objFile->close();
			}
			
			// add file path to TL_USER_CSS
			$GLOBALS[TL_JAVASCRIPT][] = $strPath;
		
			return;
		}
		// add to combined CSS string
		$GLOBALS['TL_CTB_JS'] .= $strJS;
	}

	
	
	/**
	 * Insert a template
	 *
	 * @param string $name The template name
	 * @param array  $data An optional data array
	 */
	public function insert($name, array $data=null)
	{
		
		// register the template file (to find the custom templates)
		if (!array_key_exists($name, \TemplateLoader::getFiles()))
		{
			$objTheme = \LayoutModel::findById(\ContentBlocks::getLayoutId($this->ptable, $this->pid))->getRelated('pid');
			
			\TemplateLoader::addFile($name, $objTheme->templates);
		}

		
		/** @var \Template $tpl */
		if ($this instanceof \Template)
		{
			$tpl = new static($name);
		}
		elseif (TL_MODE == 'BE')
		{
			$tpl = new \BackendTemplate($name);
		}
		else
		{
			$tpl = new \FrontendTemplate($name);
		}
		if ($data !== null)
		{
			$tpl->setData($data);
		}
		echo $tpl->parse();
	}
}
