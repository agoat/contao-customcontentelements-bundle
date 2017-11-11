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
 

class Versions extends \Contao\Controller
{
	/**
	 * Hide tl_content_value versions
	 */
	public function hideDataTableVersions ($objTemplate)
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
				$objTotal = $objDatabase->prepare("SELECT COUNT(*) AS count FROM tl_version WHERE version>1 AND editUrl IS NOT NULL" . (!$objUser->isAdmin ? " AND userid=?" : ""))
										->execute($objUser->id);

				$intLast   = ceil($objTotal->count / 30);
				$intPage   = (\Input::get('vp') !== null) ? \Input::get('vp') : 1;
				$intOffset = ($intPage - 1) * 30;


				// Create the pagination menu
				$objPagination = new \Pagination($objTotal->count, 30, 7, 'vp', new \BackendTemplate('be_pagination'));
				$objTemplate->pagination = $objPagination->generate();

				
				// Get the versions
				$objVersions = $objDatabase->prepare("SELECT pid, tstamp, version, fromTable, username, userid, description, editUrl, active FROM tl_version WHERE editUrl IS NOT NULL" . (!$objUser->isAdmin ? " AND userid=?" : "") . " ORDER BY tstamp DESC, pid, version DESC")
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
}

