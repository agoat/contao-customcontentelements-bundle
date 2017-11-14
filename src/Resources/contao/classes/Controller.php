<?php
 
 /**
 * Contao Open Source CMS - ContentBlocks extension
 *
 * Copyright (c) 2017 Arne Stappen (aGoat)
 *
 *
 * @package   contentblocks
 * @author    Arne Stappen <http://agoat.de>
 * @license	  LGPL-3.0+
 */

namespace Agoat\ContentElements;

use Contao\File;
use Contao\Combiner;


class Controller extends \Contao\Controller
{
	/**
	 * Add extra css and js to the backend template
	 */
	public function addPageLayoutToBE ($objTemplate)
	{
		if (TL_MODE == 'BE')
		{
			if ($objTemplate->getName() == 'be_main' && \Input::get('table') == 'tl_content')
			{
				if (\Input::get('do') && \Input::get('id'))
				{
					$module = \Input::get('do'); 
					
					foreach ($GLOBALS['BE_MOD'] as $group)
					{
						if (isset($group[$module]))
						{
							$moduleTables = $group[$module]['tables'];
						}
					}			

					$table = $moduleTables[(array_search('tl_content', $moduleTables)) - 1];
					
					$intLayoutId = $this->getLayoutId($table, \Input::get('id')); 

					// Sometimes the id is not the parent table but the content table id
					if (null === $intLayoutId)
					{
						$intLayoutId = $this->getLayoutId($table, \ContentModel::findById(\Input::get('id'))->pid); 
					}
				}

				$objLayout = \LayoutModel::findById($intLayoutId);

				if (null === $objLayout)
				{
					return;
				}

				$GLOBALS['TL_USER_CSS'] = array(); // The TL_USER_CSS can be reset because it's not used in the backend
				
				$this->addBackendCSS($objLayout);
				$this->addContentBlockCSS();

				$objCombiner = new Combiner();

				// Add the TL_USER_CSS to the backend template
				foreach ($GLOBALS['TL_USER_CSS'] as $stylesheet)
				{
					$options = \StringUtil::resolveFlaggedUrl($stylesheet);
					
					if ($options->static)
					{
						if ($options->mtime === null)
						{
							$options->mtime = filemtime(TL_ROOT . '/' . $stylesheet);
						}
						
						$objCombiner->add($stylesheet, $options->mtime, $options->media);
					}
					else
					{
						$objTemplate->stylesheets .= \Template::generateStyleTag(static::addStaticUrlTo($stylesheet), $options->media) . "\n";
					}							
				}
					
				if ($objCombiner->hasEntries())
				{
					foreach ($objCombiner->getFileUrls() as $strUrl)
					{
						$objTemplate->stylesheets .= \Template::generateStyleTag($strUrl, 'all') . "\n";
					}
				}
				

				$GLOBALS['TL_JAVASCRIPT'] = array(); // The TL_JAVASCRIPT can be reset because it's not used in the backend
				
				$this->addBackendJS($objLayout);
				$this->addContentBlockJS();

				$objCombiner = new Combiner();
				
				// Add the TL_JAVASCRIPT to the backend template
				foreach ($GLOBALS['TL_JAVASCRIPT'] as $javascript)
				{
					$options = \StringUtil::resolveFlaggedUrl($javascript);
					
					if ($options->static)
					{
						if ($options->mtime === null)
						{
							$options->mtime = filemtime(TL_ROOT . '/' . $javascript);
						}
						
						$objCombiner->add($javascript, $options->mtime);
					}
					else
					{
						$objTemplate->javascripts .= \Template::generateScriptTag(static::addStaticUrlTo($javascript), $options->async) . "\n";
					}							
				}
					
				if ($objCombiner->hasEntries())
				{
					foreach ($objCombiner->getFileUrls() as $strUrl)
					{
						$objTemplate->javascripts .= \Template::generateScriptTag($strUrl) . "\n";
					}
				}

				// Add jquery to the backend if active in layout
				if ($objLayout->addJQuery && $objLayout->backendJS)
				{
					$jQuery = \Template::generateScriptTag('assets/jquery/js/jquery.min.js') . "\n";
					$jQuery .= "<script>jQuery.noConflict();</script>\n";
					$objTemplate->javascripts = $jQuery . $objTemplate->javascripts;
				}
			}
		}
	}

	
	public function addContentBlockCSS ($strBuffer='', $objTemplate=null)
	{
		foreach (array('CSS', 'SCSS' , 'LESS') as $strType)
		{
			if ($GLOBALS['TL_CTB_' . $strType] == '')
			{
				continue;
			}

			$strKey = substr(md5($strType . $GLOBALS['TL_CTB_CSS'] . $GLOBALS['TL_CTB_SCSS'] . $GLOBALS['TL_CTB_LESS']), 0, 12);
			$strPath = 'assets/css/' . $strKey . '.' . strtolower($strType);
			
			// Write to a temporary file in the assets folder
			if (!file_exists($strPath))
			{
				$objFile = new File($strPath, true);
				$objFile->write($GLOBALS['TL_CTB_' . $strType]);
				$objFile->close();
			}
				
			$strPath .= '|static';
			
			// add file path to TL_USER_CSS
			$GLOBALS['TL_USER_CSS'][] = $strPath;
		}

		return $strBuffer;
	}

	public function addContentBlockJS ($strBuffer='', $objTemplate=null)
	{
		if ($GLOBALS['TL_CTB_JS'] == '')
		{
			return $strBuffer;
		}

		$strKey = substr(md5('js' . $GLOBALS['TL_CTB_JS']), 0, 12);
		$strPath = 'assets/js/' . $strKey . '.js';
		
		// Write to a temporary file in the assets folder
		if (!file_exists($strPath))
		{
			$objFile = new File($strPath, true);
			$objFile->write($GLOBALS['TL_CTB_JS']);
			$objFile->close();
		}
			
		$strPath .= '|static';
		
		// add file path to TL_JAVASCRIPT
		$GLOBALS['TL_JAVASCRIPT'][] = $strPath;

		return $strBuffer;
	}

	
	public function addLayoutJS ($objPage, $objLayout)
	{
		$arrExternalJS = \StringUtil::deserialize($objLayout->externalJS);
		
		if (!empty($arrExternalJS) && is_array($arrExternalJS))
		{
			// Consider the sorting order (see #5038)
			if ($objLayout->orderBackendJS != '')
			{
				$tmp = \StringUtil::deserialize($objLayout->orderExternalJS);
				
				if (!empty($tmp) && is_array($tmp))
				{
					// Remove all values
					$arrOrder = array_map(function(){}, array_flip($tmp));
					
					// Move the matching elements to their position in $arrOrder
					foreach ($arrExternalJS as $k=>$v)
					{
						if (array_key_exists($v, $arrOrder))
						{
							$arrOrder[$v] = $v;
							unset($arrExternalJS[$k]);
						}
					}
					
					// Append the left-over style sheets at the end
					if (!empty($arrExternalJS))
					{
						$arrOrder = array_merge($arrOrder, array_values($arrExternalJS));
					}
					
					// Remove empty (unreplaced) entries
					$arrExternalJS = array_values(array_filter($arrOrder));
					unset($arrOrder);
				}
			}
			
			// Get the file entries from the database
			$objFiles = \FilesModel::findMultipleByUuids($arrExternalJS);
			
			if ($objFiles !== null)
			{
				while ($objFiles->next())
				{
					if (file_exists(TL_ROOT . '/' . $objFiles->path))
					{
						$GLOBALS['TL_JAVASCRIPT'][] = $objFiles->path . '|static';
					}
				}
			}

			unset($objFiles);
		}
					
		return;
	}
		
	
	/**
	 * Register content block elements to the DCA
	 *
	 */
	public function registerBlockElements ()
	{
		// Don´t register twice
		if (isset($GLOBALS['TL_CTE']['CTE'])) 
		{
			return;
		}

		$db = \Database::getInstance();

		if ($db->tableExists("tl_elements"))
		{		
			$arrElements = $db->prepare("SELECT * FROM tl_elements ORDER BY sorting ASC")
							  ->execute()
							  ->fetchAllAssoc();	
		}
			
		if ($arrElements === null)
		{
			return;
		}

		// Add content blocks as content elements
		foreach ($arrElements as $arrElement)
		{
			$GLOBALS['TL_CTE']['CTE'][$arrElement['alias']] = 'Agoat\ContentElements\ContentElement';
			$GLOBALS['TL_LANG']['CTE'][$arrElement['alias']] = array($arrElement['title'],$arrElement['description']);
		}
	}


	/*
	 * Dynamically change parent table when editing subpattern
	 */
	public function handleSubPatternTable ($strTable)
	{
		if ($strTable == 'tl_pattern')
		{
			if (\Input::get('spid') !== null)
			{
				$objParent = \PatternModel::findById(\Input::get('spid'));
			}
			else if (\Input::get('act') == 'edit' && \Input::get('mode') == '1')
			{
				// For the 'save and edit' function use the parent of the parent to check for a sub pattern
				$objParent = \PatternModel::findById(\Input::get('pid'));

				if ($objParent->ptable == 'tl_subpattern')
				{
					$objParent = \PatternModel::findById($objParent->pid);
				}
				else
				{
					$objParent = null;
				}
			}
			else
			{
				$objParent = null;	
			}

			if (null !== $objParent)
			{
				if (\Input::get('id') == \Input::get('spid') && \Input::get('act') == 'edit')
				{
					// When editing a sub pattern itself use the ptable of the sub pattern object
					$GLOBALS['TL_DCA']['tl_pattern']['config']['ptable'] = $objParent->ptable;
				}
				else
				{
					$GLOBALS['TL_DCA']['tl_pattern']['config']['ptable'] = 'tl_subpattern';
				}
				
				$GLOBALS['TL_DCA']['tl_pattern']['list']['sorting']['headerFields'] =  array('type','alias');

				// Extra config for the sub pattern types
				if ($objParent->type == 'subpattern')
				{
					$GLOBALS['TL_DCA']['tl_pattern']['list']['sorting']['headerFields'][] =  'subPatternType';
					
					// Set the filter for the subpattern option
					if ($objParent->subPatternType == 'options')
					{
						// Set callbacks and filter
						$GLOBALS['TL_DCA']['tl_pattern']['config']['onload_callback'][] = array('tl_pattern', 'subPatternFilter');
						$GLOBALS['TL_DCA']['tl_pattern']['list']['sorting']['panel_callback']['subPatternFilter'] = array('tl_pattern', 'generatesubPatternFilter');

						$GLOBALS['TL_DCA']['tl_pattern']['list']['sorting']['panelLayout'] = str_replace('filter', 'subPatternFilter;filter', $GLOBALS['TL_DCA']['tl_pattern']['list']['sorting']['panelLayout']);
					}
				}
				else if ($objParent->type == 'multipattern')
				{
					$GLOBALS['TL_DCA']['tl_pattern']['list']['sorting']['headerFields'][] =  'numberOfGroups';
				}
			}
		}
	}

	
	/**
	 * Get the rootpage ID for an article
	 *
	 * @param string  $strTable The name of the table (article or news) 
	 * @param integer $intId    An article or a news article ID
	 *
	 * @return integer The theme ID
	 */
	public static function getRootPageId ($strTable, $intId)
	{
		if ($strTable == 'tl_article')
		{
			$objArticle = \ArticleModel::findById($intId);

			if ($objArticle === null)
			{
				return null;
			}
			
			$objPage = \PageModel::findWithDetails($objArticle->pid);

			if ($objPage === null)
			{
				return null;
			}

			return $objPage->rootId;
		}
		
		elseif($strTable == 'tl_news')
		{
			$objNews = \NewsModel::findById($intId);

			if ($objNews === null)
			{
				return null;
			}
			
			$objPage = \PageModel::findWithDetails($objNews->getRelated('pid')->jumpTo);

			if ($objPage === null)
			{
				return null;
			}
			
			return $objPage->rootId;
		}
		
		else
		{
			// HOOK: custom method to discover the layout id
			if (isset($GLOBALS['TL_HOOKS']['getRootPageId']) && is_array($GLOBALS['TL_HOOKS']['getRootPageId']))
			{
				foreach ($GLOBALS['TL_HOOKS']['getRootPageId'] as $callback)
				{
					//$this->import($callback[0]);
					$rootId = static::importStatic($callback[0])->{$callback[1]}($strTable, $intId);
			
					if (rootId)
					{
						return $rootId;
					}
				}
			}
		}
		
		return null;
	}
	

	/**
	 * Get the layout ID for an article
	 *
	 * @param string  $strTable The name of the table (article or news) 
	 * @param integer $intId    An article or a news article ID
	 *
	 * @return integer The theme ID
	 */
	public static function getLayoutId ($strTable, $intId)
	{
		if ($strTable == 'tl_article')
		{
			$objArticle = \ArticleModel::findById($intId);

			if ($objArticle === null)
			{
				return null;
			}
			
			$objPage = \PageModel::findWithDetails($objArticle->pid);

			if ($objPage === null)
			{
				return null;
			}

			return $objPage->layout;
		}
		
		elseif($strTable == 'tl_news')
		{
			$objNews = \NewsModel::findById($intId);

			if ($objNews === null)
			{
				return null;
			}
			
			$objPage = \PageModel::findWithDetails($objNews->getRelated('pid')->jumpTo);

			if ($objPage === null)
			{
				return null;
			}
			
			return $objPage->layout;
		}
		
		else
		{
			// HOOK: custom method to discover the layout id
			if (isset($GLOBALS['TL_HOOKS']['getLayoutId']) && is_array($GLOBALS['TL_HOOKS']['getLayoutId']))
			{
				foreach ($GLOBALS['TL_HOOKS']['getLayoutId'] as $callback)
				{
					//$this->import($callback[0]);
					$layoutId = static::importStatic($callback[0])->{$callback[1]}($strTable, $intId);
			
					if (layoutId)
					{
						return $layoutId;
					}
				}
			}
		}
		
		return null;
	}


	/**
	 * Add the backend CSS of the layout to the $GLOBALS['TL_USER_CSS']
	 *
	 * @param object  $objLayout The layout object
	 *
	 */
	private static function addBackendCSS($objLayout)
	{
		$arrCSS = \StringUtil::deserialize($objLayout->backendCSS);
		
		if (!empty($arrCSS) && is_array($arrCSS))
		{
			// Consider the sorting order (see #5038)
			if ($objLayout->orderBackendCSS != '')
			{
				$tmp = \StringUtil::deserialize($objLayout->orderBackendCSS);
				
				if (!empty($tmp) && is_array($tmp))
				{
					// Remove all values
					$arrOrder = array_map(function(){}, array_flip($tmp));
					
					// Move the matching elements to their position in $arrOrder
					foreach ($arrCSS as $k=>$v)
					{
						if (array_key_exists($v, $arrOrder))
						{
							$arrOrder[$v] = $v;
							unset($arrCSS[$k]);
						}
					}
					
					// Append the left-over style sheets at the end
					if (!empty($arrCSS))
					{
						$arrOrder = array_merge($arrOrder, array_values($arrCSS));
					}
					
					// Remove empty (unreplaced) entries
					$arrCSS = array_values(array_filter($arrOrder));
					unset($arrOrder);
				}
			}
			
			// Get the file entries from the database
			$objFiles = \FilesModel::findMultipleByUuids($arrCSS);
			
			if ($objFiles !== null)
			{
				while ($objFiles->next())
				{
					if (file_exists(TL_ROOT . '/' . $objFiles->path))
					{
						$GLOBALS['TL_USER_CSS'][] = $objFiles->path . '|static';
					}
				}
			}

			unset($objFiles);
		}
	}
	
	/**
	 * Add the backend JS of the layout to the $GLOBALS['TL_JAVASCRIPT']
	 *
	 * @param object  $objLayout The layout object
	 *
	 */
	private static function addBackendJS($objLayout)
	{
		$arrJS = \StringUtil::deserialize($objLayout->backendJS);
		
		if (!empty($arrJS) && is_array($arrJS))
		{
			// Consider the sorting order (see #5038)
			if ($objLayout->orderBackendJS != '')
			{
				$tmp = \StringUtil::deserialize($objLayout->orderBackendJS);
				
				if (!empty($tmp) && is_array($tmp))
				{
					// Remove all values
					$arrOrder = array_map(function(){}, array_flip($tmp));
					
					// Move the matching elements to their position in $arrOrder
					foreach ($arrJS as $k=>$v)
					{
						if (array_key_exists($v, $arrOrder))
						{
							$arrOrder[$v] = $v;
							unset($arrJS[$k]);
						}
					}
					
					// Append the left-over style sheets at the end
					if (!empty($arrJS))
					{
						$arrOrder = array_merge($arrOrder, array_values($arrJS));
					}
					
					// Remove empty (unreplaced) entries
					$arrJS = array_values(array_filter($arrOrder));
					unset($arrOrder);
				}
			}
			
			// Get the file entries from the database
			$objFiles = \FilesModel::findMultipleByUuids($arrJS);
			
			if ($objFiles !== null)
			{
				while ($objFiles->next())
				{
					if (file_exists(TL_ROOT . '/' . $objFiles->path))
					{
						$GLOBALS['TL_JAVASCRIPT'][] = $objFiles->path . '|static';
					}
				}
			}

			unset($objFiles);
		}
	}
	
	/**
	 * register callbacks for news extension bundles with contao core
	 */
	public function setNewsArticleCallbacks ($strTable)
	{
		if ($strTable != 'tl_news' || TL_MODE == 'FE')
		{
			return;
		}
		
		$GLOBALS['TL_DCA']['tl_news']['config']['oncopy_callback'][] = array('tl_news_contentblocks', 'copyRelatedValues');
		$GLOBALS['TL_DCA']['tl_news']['config']['ondelete_callback'][] = array('tl_news_contentblocks', 'deleteRelatedValues');

	}
}

