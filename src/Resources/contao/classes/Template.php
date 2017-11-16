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

namespace Agoat\ContentElements;

use Contao\File;
use Contao\TemplateLoader;
use Contao\FrontendTemplate;
use Contao\BackendTemplate;
use Contao\StringUtil;
use Agoat\ContentElements\Controller;
use Agoat\ContentElements\Pattern;


class Template extends FrontendTemplate
{
	
	/**
	 * Add a wrapper in the backend
	 *
	 * @return string The template markup
	 */
	public function parse()
	{
		$strBuffer = parent::parse();

		if (TL_MODE == 'BE')
		{
			$strBuffer = '<div class="tl_ce ' . $this->strTemplate . '">' . $strBuffer . '</div>';
		}
		
		return $strBuffer;
	}


	/**
	 * Add Image to template
	 *
	 * @param object $image The element or module as array
	 * @param object|Model $size The image size as 
	 * @param integer $width An optional width of the image
	 * @param integer $height An optional height of the image
	 *
	 * @return object  new image
	 */
	public function addImage ($image, $size, $width=0, $height=0)
	{
		if (!is_array($image))
		{
			return;
		}
		
		
		if (@unserialize($size) === false)
		{
			$size = serialize(array((string)$width, (string)$height, $size));
		}
		
		$image['size'] = $size;
		$image['singleSRC'] = $image['path'];
		
		Pattern::addImageToTemplate($picture = new \stdClass(), $image);	
		
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
		
		$strType = strtoupper($strType);
		
		if (!in_array($strType, array('CSS', 'SCSS' , 'LESS')))
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
				$objFile = new File($strPath, true);
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
				$objFile = new File($strPath, true);
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
	 * Save a variable globally (to load in other content block templates)
	 *
	 * @param string $strKey  The name
	 * @param mixed  $varData The content
	 */
	public function saveVar ($strKey, $varData)
	{
		$GLOBALS['templateVars'][$strKey] = $varData;
	}

	
	/**
	 * Load a variable globally
	 *
	 * @param string $strKey  The name
	 *
	 * @return mixed|boolean The globally saved content or false if nothing exist
	 */
	public function loadVar ($strKey)
	{
		if (!isset($GLOBALS['templateVars'][$strKey]))
		{
			return null;
		}
		
		return $GLOBALS['templateVars'][$strKey];
	}

	
	/**
	 * Return the alias of the previous content element
	 *
	 * @return string  The type of the previous content element
	 */
	public function prevElement ()
	{
		if (($arrTypes = $GLOBALS['templateTypes'][$this->ptable.'.'.$this->pid]) === null)
		{
			$objCte = \ContentModel::findPublishedByPidAndTable($this->pid, $this->ptable);
			
			if ($objCte === null)
			{
				return;
			}
			
			$arrTypes = $GLOBALS['templateTypes'][$this->ptable.'.'.$this->pid] = $objCte->fetchEach('type');
		}

		return $arrTypes[array_keys($arrTypes)[array_search($this->id, array_keys($arrTypes)) - 1]];
	}

	
	/**
	 * Return the alias of the previous content element
	 *
	 * @return string  The type of the previous content element
	 */
	public function nextElement ()
	{
		if (($arrTypes = $GLOBALS['templateTypes'][$this->ptable.'.'.$this->pid]) === null)
		{
		$objCte = \ContentModel::findPublishedByPidAndTable($this->pid, $this->ptable);
			
			if ($objCte === null)
			{
				return;
			}
			
			$arrTypes = $GLOBALS['templateTypes'][$this->ptable.'.'.$this->pid] = $objCte->fetchEach('type');
		}
		
		return $arrTypes[array_keys($arrTypes)[array_search($this->id, array_keys($arrTypes)) + 1]];
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
		if (!array_key_exists($name, TemplateLoader::getFiles()))
		{
			$objTheme = \LayoutModel::findById(Controller::getLayoutId($this->ptable, $this->pid))->getRelated('pid');
			
			TemplateLoader::addFile($name, $objTheme->templates);
		}

		
		/** @var \Template $tpl */
		if ($this instanceof \Template)
		{
			$tpl = new static($name);
		}
		elseif (TL_MODE == 'BE')
		{
			$tpl = new BackendTemplate($name);
		}
		else
		{
			$tpl = new FrontendTemplate($name);
		}
		if ($data !== null)
		{
			$tpl->setData($data);
		}
		echo $tpl->parse();
	}
}
