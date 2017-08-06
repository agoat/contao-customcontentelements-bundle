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

namespace Agoat\ContentBlocks;
 
use Contao\Input;
use Contao\File;
use Contao\StringUtil;
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
			if ($objTemplate->getName() == 'be_main' && Input::get('table') == 'tl_content')
			{
				if (Input::get('do') && Input::get('id'))
				{
					$intLayoutId = $this->getLayoutId('tl_'.Input::get('do'), Input::get('id')); 
					
					// Sometimes the id is not the parent table but the content table id
					if (!$intLayoutId)
					{
						$intLayoutId = $this->getLayoutId('tl_'.Input::get('do'), \ContentModel::findById(Input::get('id'))->pid); 
					}
				}

				$objLayout = \LayoutModel::findById($intLayoutId);

				if ($objLayout === null)
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

				// Add jquery to the backend if active in layout
				if ($objLayout->addJQuery && $objLayout->backendJS)
				{
					array_unshift($GLOBALS['TL_JAVASCRIPT'], 'assets/jquery/js/jquery.min.js');
				}
				
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
						$objTemplate->javascripts = \Template::generateScriptTag($strUrl) . "\n" . $objTemplate->javascripts;
					}
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
		$arrExternalJS = StringUtil::deserialize($objLayout->externalJS);
		
		if (!empty($arrExternalJS) && is_array($arrExternalJS))
		{
			// Consider the sorting order (see #5038)
			if ($objLayout->orderBackendJS != '')
			{
				$tmp = StringUtil::deserialize($objLayout->orderExternalJS);
				
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
	 * Hide tl_content_value versions
	 */
	public function hideContentValueVersions ($objTemplate)
	{
		if ($objTemplate instanceof \BackendTemplate)
		{
			// Change the version and pagination in the welcome screen
			if ($objTemplate->getName() == 'be_welcome')
			{
				$arrVersions = array();

				$objUser = \BackendUser::getInstance();
				$objDatabase = \Database::getInstance();

				// Get the total number of versions
				$objTotal = $objDatabase->prepare("SELECT COUNT(*) AS count FROM tl_version WHERE NOT fromTable=\"tl_content_value\" AND version>1" . (!$objUser->isAdmin ? " AND userid=?" : ""))
										->execute($objUser->id);

				$intLast   = ceil($objTotal->count / 30);
				$intPage   = (\Input::get('vp') !== null) ? \Input::get('vp') : 1;
				$intOffset = ($intPage - 1) * 30;


				// Create the pagination menu
				$objPagination = new \Pagination($objTotal->count, 30, 7, 'vp', new \BackendTemplate('be_pagination'));
				$objTemplate->pagination = $objPagination->generate();

				
				// Get the versions
				$objVersions = $objDatabase->prepare("SELECT pid, tstamp, version, fromTable, username, userid, description, editUrl, active FROM tl_version WHERE NOT fromTable=\"tl_content_value\"" . (!$objUser->isAdmin ? " AND userid=?" : "") . " ORDER BY tstamp DESC, pid, version DESC")
										   ->limit(30, $intOffset)
										   ->execute($objUser->id);

				while ($objVersions->next())
				{
					$arrRow = $objVersions->row();

					// Add some parameters
					$arrRow['from'] = max(($objVersions->version - 1), 1); // see #4828
					$arrRow['to'] = $objVersions->version;
					$arrRow['date'] = date(\Config::get('datimFormat'), $objVersions->tstamp);
					$arrRow['description'] = \StringUtil::substr($arrRow['description'], 32);
					$arrRow['shortTable'] = \StringUtil::substr($arrRow['fromTable'], 18); // see #5769

					if ($arrRow['editUrl'] != '')
					{
						$arrRow['editUrl'] = preg_replace('/&(amp;)?rt=[^&]+/', '&amp;rt=' . REQUEST_TOKEN, ampersand($arrRow['editUrl']));
					}

					$arrVersions[] = $arrRow;
				}
				
				$intCount = -1;
				$arrVersions = array_values($arrVersions);
				
				// Add the "even" and "odd" classes
				foreach ($arrVersions as $k=>$v)
				{
					$arrVersions[$k]['class'] = (++$intCount % 2 == 0) ? 'even' : 'odd';
					
					try
					{
						// Mark deleted versions (see #4336)
						$objDeleted = $objDatabase->prepare("SELECT COUNT(*) AS count FROM " . $v['fromTable'] . " WHERE id=?")
												  ->execute($v['pid']);
						
						$arrVersions[$k]['deleted'] = ($objDeleted->count < 1);
					}
					catch (\Exception $e)
					{
						// Probably a disabled module
						--$intCount;
						unset($arrVersions[$k]);
					}
					
					// Skip deleted files (see #8480)
					if ($v['fromTable'] == 'tl_files' && $arrVersions[$k]['deleted'])
					{
						--$intCount;
						unset($arrVersions[$k]);
					}
				}
				
				$objTemplate->versions = $arrVersions;			
			}
		}
	}
	
		
	
	// register new content elements (for content element class assignment)
	public function loadAndRegisterBlockElements ()
	{
		// Don´t register twice
		if (is_array($GLOBALS['TL_CTB'])) 
		{
			return;
		}

		$this->import("Database");

		if ($this->Database->tableExists("tl_content_blocks"))
		{		
			// get all elements in db
			$arrElements 	= $this->Database->prepare("SELECT * FROM tl_content_blocks ORDER BY sorting ASC")
											->execute()
											->fetchAllAssoc();	
		}
			
		if ($arrElements === null)
		{
			return;
		}

		// generate array with elements 
		foreach ($arrElements as $arrElement)
		{
			$arrCTE['ctb'][$arrElement['alias']] = 'Agoat\ContentBlocks\ContentBlockElement';			
		}

		// add to registry
		$GLOBALS['TL_CTB'] = $arrCTE; // content block elements
		
		array_insert($GLOBALS['TL_CTE'], 0, $arrCTE); // add to content elements array

	}

	
	// register new content elements (for content element selection)
	public function loadAndRegisterElementsWithGroups ($strTable)
	{
		if ($strTable != 'tl_content' || TL_MODE == 'FE')
		{
			return;
		}

		$this->import("Database");

		if ($this->Database->tableExists("tl_content_blocks"))
		{		
			// get all content block elements from db
			$arrElements 	= $this->Database->prepare("SELECT * FROM tl_content_blocks ORDER BY pid, sorting ASC")
											->execute()
											->fetchAllAssoc();	
		}
			
		if ($arrElements === null)
		{
			return;
		}


		// generate array with elements 
		foreach ($arrElements as $arrElement)
		{
			// group
			if ($arrElement['type'] == 'group')
			{
				$strGroup = $arrElement['alias'];
				$arrLANG[$arrElement['alias']] = $arrElement['title'];	

				if (!isset($arrCTB[$arrElement['pid']]))
				{
					$arrCTB[$arrElement['pid']] = array();
				}
				
			}
			else
			{
				if (!isset($arrCTB[$arrElement['pid']]))
				{
					$strGroup = 'ctb';
				}
				
				$arrCTE[$strGroup][$arrElement['alias']] = 'Agoat\ContentBlocks\ContentBlockElement';
				
				if (!$arrElement['invisible'])
				{
					$arrCTB[$arrElement['pid']][$strGroup][$arrElement['alias']] = 'ContentBlockElement';					
				}
				
				$arrCBI[$arrElement['alias']] = $arrElement['singleSRC'];
				$arrLANG[$arrElement['alias']] = array($arrElement['title'],$arrElement['description']);
				
				// set as default element type
				if ($arrElement['defaultType'])
				{
					$GLOBALS['TL_CTB_DEFAULT'][$arrElement['pid']] = $arrElement['alias'];
				}
				elseif (!isset($GLOBALS['TL_CTB_DEFAULT'][$arrElement['pid']]))
				{
					$GLOBALS['TL_CTB_DEFAULT'][$arrElement['pid']] = $arrElement['alias'];
				}
			}
		}
	
		// add to registry
		$GLOBALS['TL_CTB'] = $arrCTB; // content block elements
		$GLOBALS['TL_CTB_IMG'] = $arrCBI; // content block images
		
		$GLOBALS['TL_CTE_LEGACY'] = $GLOBALS['TL_CTE']; // save contao standard content elements for legacy support
		array_insert($GLOBALS['TL_CTE'], 0, $arrCTE); // add to content elements array
		
		array_insert($GLOBALS['TL_LANG']['CTE'], 0, $arrLANG); // add to language 


	}

	
	/**
	 * Get the theme ID for an article
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
				return false;
			}
			
			$objPage = \PageModel::findWithDetails($objArticle->pid);

			if ($objPage === null)
			{
				return false;
			}

			return $objPage->layout;
			
		}
		elseif($strTable == 'tl_news')
		{
			$objNews = \NewsModel::findById($intId);

			if ($objNews === null)
			{
				return false;
			}
			
			$objPage = \PageModel::findWithDetails($objNews->getRelated('pid')->jumpTo);

			if ($objPage === null)
			{
				return false;
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
					$this->import($callback[0]);
					$intId = $this->{$callback[0]}->{$callback[1]}($strTable, $intId);
				}
			}
			return $intId;
		}
	
	}


	/**
	 * Add the backend CSS of the layout to the $GLOBALS['TL_USER_CSS']
	 *
	 * @param object  $objLayout The layout object
	 *
	 */
	private static function addBackendCSS($objLayout)
	{
		$arrCSS = StringUtil::deserialize($objLayout->backendCSS);
		
		if (!empty($arrCSS) && is_array($arrCSS))
		{
			// Consider the sorting order (see #5038)
			if ($objLayout->orderBackendCSS != '')
			{
				$tmp = StringUtil::deserialize($objLayout->orderBackendCSS);
				
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
		$arrJS = StringUtil::deserialize($objLayout->backendJS);
		
		if (!empty($arrJS) && is_array($arrJS))
		{
			// Consider the sorting order (see #5038)
			if ($objLayout->orderBackendJS != '')
			{
				$tmp = StringUtil::deserialize($objLayout->orderBackendJS);
				
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

