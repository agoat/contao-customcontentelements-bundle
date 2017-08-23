<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Agoat\ContentElements;

use Patchwork\Utf8;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


/**
 * Provide methods to export/import content blocks and pattern
 *
 * @author Arne Stappen (aGoat) <https://github.com/agoat>
 */
class Theme extends \Contao\Theme
{

	/**
	 * Import content blocks tables with template import
	 *
	 * compareThemeFiles Hook
	 */
	public function compareTables ($xml, $objArchive)
	{
		// Store the field names of the theme tables
		$arrDbFields = array
		(
			'tl_elements'	=> $this->Database->getFieldNames('tl_elements'),
			'tl_pattern'	=> $this->Database->getFieldNames('tl_pattern')
		);
			
		$tables = $xml->getElementsByTagName('table');

		$blnHasError = false;

		// Loop through the tables
		for ($i=0; $i<$tables->length; $i++)
		{
			$rows = $tables->item($i)->childNodes;
			$table = $tables->item($i)->getAttribute('name');

			// Skip invalid tables
			if (!in_array($table, array_keys($arrDbFields)))
			{
				continue;
			}

			$arrFieldNames = array();

			// Loop through the rows
			for ($j=0; $j<$rows->length; $j++)
			{
				$fields = $rows->item($j)->childNodes;

				// Loop through the fields
				for ($k=0; $k<$fields->length; $k++)
				{
					$arrFieldNames[$fields->item($k)->getAttribute('name')] = true;
				}
			}

			$arrFieldNames = array_keys($arrFieldNames);

			// Loop through the fields
			foreach ($arrFieldNames as $name)
			{
				// Print a warning if a field is missing
				if (!in_array($name, $arrDbFields[$table]))
				{
					$return .= "\n  " . '<p class="tl_red" style="margin:0">'. sprintf($GLOBALS['TL_LANG']['tl_theme']['missing_field'], $table .'.'. $name) .'</p>';
				}
			}
		}

		// Confirmation
		if (!$blnHasError)
		{
			$return .= "\n  " . '<p class="tl_green" style="margin:0">'. $GLOBALS['TL_LANG']['tl_theme']['tables_ok'] .'</p>';
		}
	
		return "\n" . '<h4>'.$GLOBALS['TL_LANG']['tl_theme']['elements'].'</h4>' . "\n" . $return;
	}

	
	
	/**
	 * Import content blocks tables with template import
	 *
	 * extractThemeFiles Hook
	 */
	public function importTables ($xml, $objArchive, $intThemeId, $arrMapper)
	{
		// Store the field names of the theme tables
		$arrDbFields = array
		(
			'tl_elements'	=> $this->Database->getFieldNames('tl_elements'),
			'tl_pattern'	=> $this->Database->getFieldNames('tl_pattern')
		);
			
		// Lock the tables
		$arrLocks = array
		(
			'tl_elements'	=> 'WRITE',
			'tl_pattern'	=> 'WRITE',
			'tl_subpattern'	=> 'WRITE',
			'tl_files'		=> 'READ',
			'tl_theme'		=> 'READ'
		);

		// Load the DCAs of the locked tables (see #7345)
		foreach (array_keys($arrLocks) as $table)
		{
			$this->loadDataContainer($table);
		}

		$this->Database->lockTables($arrLocks);

		// Get the current auto_increment values
		$tl_elements = $this->Database->getNextId('tl_elements');
		$tl_pattern = $this->Database->getNextId('tl_pattern');

		
		$tables = $xml->getElementsByTagName('table');
		
		// Update the mapper data first
		for ($i=0; $i<$tables->length; $i++)
		{
			$rows = $tables->item($i)->childNodes;
			$table = $tables->item($i)->getAttribute('name');

			// Skip all other tables
			if (!in_array($table, array_keys($arrDbFields)))
			{
				continue;
			}

			// Loop through the rows
			for ($j=0; $j<$rows->length; $j++)
			{
				$fields = $rows->item($j)->childNodes;

				// Loop through the fields
				for ($k=0; $k<$fields->length; $k++)
				{
					// Increment the ID
					if ($fields->item($k)->getAttribute('name') == 'id')
					{
						$arrMapper[$table][$fields->item($k)->nodeValue] = ${$table}++;
						break;
					}
				}
			}
		}

		// Loop through the tables
		for ($i=0; $i<$tables->length; $i++)
		{
			$rows = $tables->item($i)->childNodes;
			$table = $tables->item($i)->getAttribute('name');

			// Skip invalid tables
			if (!in_array($table, array_keys($arrDbFields)))
			{
				continue;
			}

			// Get the order fields
			$objDcaExtractor = \DcaExtractor::getInstance($table);
			$arrOrder = $objDcaExtractor->getOrderFields();

			// Loop through the rows
			for ($j=0; $j<$rows->length; $j++)
			{
				$set = array();
				$fields = $rows->item($j)->childNodes;

				// Loop through the fields
				for ($k=0; $k<$fields->length; $k++)
				{
					$value = $fields->item($k)->nodeValue;
					$name = $fields->item($k)->getAttribute('name');

					// Skip NULL values
					if ($value == 'NULL')
					{
						continue;
					}

					// Increment the ID
					elseif ($name == 'id')
					{
						$value = $arrMapper[$table][$value];
					}

					// Increment the parent IDs
					elseif ($name == 'pid')
					{
						if ($table == 'tl_pattern')
						{
							if (isset($arrMapper['tl_elements'][$value]))
							{
								$value = $arrMapper['tl_elements'][$value];
							}
							else
							{
								$value = $arrMapper['tl_pattern'][$value];
							}
						}
						else
						{
							$value = $arrMapper['tl_theme'][$value];
						}
					}

					// Adjust element alias
					elseif ($table == 'tl_elements' && $name == 'alias')
					{
						$objName = $this->Database->prepare("SELECT name FROM tl_theme WHERE id=?")
												  ->limit(1)
												  ->execute($set['pid']);

						$value = \StringUtil::generateAlias($objName->name . '-' . $set['title']);			  
					}


					// Replace the file paths in singleSRC fields with their tl_files ID
					elseif ($GLOBALS['TL_DCA'][$table]['fields'][$name]['inputType'] == 'fileTree' && !$GLOBALS['TL_DCA'][$table]['fields'][$name]['eval']['multiple'])
					{
						if (!$value)
						{
							$value = null; // Contao >= 3.2
						}
						else
						{
							// Do not use the FilesModel here - tables are locked!
							$objFile = $this->Database->prepare("SELECT uuid FROM tl_files WHERE path=?")
													  ->limit(1)
													  ->execute($this->customizeUploadPath($value));

							$value = $objFile->uuid;
						}
					}

					// Replace the file paths in multiSRC fields with their tl_files ID
					elseif ($GLOBALS['TL_DCA'][$table]['fields'][$name]['inputType'] == 'fileTree' || in_array($name, $arrOrder))
					{
						$tmp = \StringUtil::deserialize($value);

						if (is_array($tmp))
						{
							foreach ($tmp as $kk=>$vv)
							{
								// Do not use the FilesModel here - tables are locked!
								$objFile = $this->Database->prepare("SELECT uuid FROM tl_files WHERE path=?")
														  ->limit(1)
														  ->execute($this->customizeUploadPath($vv));

								$tmp[$kk] = $objFile->uuid;
							}

							$value = serialize($tmp);
						}
					}

					// Adjust the imageSize widget data
					elseif ($GLOBALS['TL_DCA'][$table]['fields'][$name]['inputType'] == 'imageSize')
					{
						$imageSizes = \StringUtil::deserialize($value, true);

						if (!empty($imageSizes))
						{
							if (is_numeric($imageSizes[2]))
							{
								$imageSizes[2] = $arrMapper['tl_image_size'][$imageSizes[2]];
							}
						}

						$value = serialize($imageSizes);
					}

					// Adjust the sizeList
					elseif ($table == 'tl_pattern' && $name == 'sizeList')
					{
						$imageSizes = \StringUtil::deserialize($value, true);

						if (!empty($imageSizes))
						{
							foreach ($imageSizes as $kk=>$vv)
							{
								$imageSizes[$kk] = $arrMapper['tl_image_size'][$vv];
								if (empty($imageSizes[$kk])) unset($imageSizes[$kk]); // Remove if no new size could be found
							}
						}
						
						$value = serialize($imageSizes);
					}

					$set[$name] = $value;
				}

				// Skip fields that are not in the database (e.g. because of missing extensions)
				foreach ($set as $k=>$v)
				{
					if (!in_array($k, $arrDbFields[$table]))
					{
						unset($set[$k]);
					}
				}
			
				// Insert into database
				$this->Database->prepare("INSERT INTO $table %s")->set($set)->execute();

				// Insert subpattern into database
				if ($table == 'tl_pattern' && Pattern::isSubPattern($set['type']))
				{
					$subSet['id'] = $set['id'];
					$subSet['pid'] = $set['id'];
					$subSet['type'] = $set['type'];
					$subSet['title'] = $set['label'];
					$subSet['alias'] = $set['alias'];
					$subSet['subPatternType	'] = $set['subPatternType'];
					$subSet['numberOfGroups	'] = $set['numberOfGroups'];

					$this->Database->prepare("INSERT INTO tl_subpattern %s")->set($subSet)->execute();
				}
			}
		}

		// Unlock the tables
		$this->Database->unlockTables();
	
		unset($tl_elements, $tl_pattern);
	}
	
	
	/**
	 * Export content blocks tables with template export
	 *
	 * exportTheme Hook
	 */
	public function exportTables ($xml, $objArchive, $objThemeId)
	{

		// Find tables node
		$tables = $xml->getElementsByTagName('tables')[0];
		
		// Add the elements table
		$table = $xml->createElement('table');
		$table->setAttribute('name', 'tl_elements');
		$table = $tables->appendChild($table);

		// Load the DCA
		$this->loadDataContainer('tl_elements');

		// Get the order fields
		$objDcaExtractor = \DcaExtractor::getInstance('tl_elements');
		$arrOrder = $objDcaExtractor->getOrderFields();

		// Get all content blocks
		$objElements = $this->Database->prepare("SELECT * FROM tl_elements WHERE pid=?")
										->execute($objThemeId);

		// Add the rows
		while ($objElements->next())
		{
			$this->addDataRow($xml, $table, $objElements->row(), $arrOrder);
		}

		$objElements->reset();
		
		// Add the pattern table
		$table = $xml->createElement('table');
		$table->setAttribute('name', 'tl_pattern');
		$table = $tables->appendChild($table);

		// Load the DCA
		$this->loadDataContainer('tl_pattern');

		// Get the order fields
		$objDcaExtractor = \DcaExtractor::getInstance('tl_pattern');
		$arrOrder = $objDcaExtractor->getOrderFields();


		// Add pattern recursively
		while ($objElements->next())
		{
			$this->addPatternData($xml, $table, $arrOrder, $objElements->id);
		}
	}

	
	/**
	 * Add pattern recursively
	 */
	protected function addPatternData ($xml, $table, $arrOrder, $intParentID)
	{
		// Get all content patterns
		$objPattern = $this->Database->prepare("SELECT * FROM tl_pattern WHERE pid=?")
						   ->execute($intParentID);
		
		// Add the rows
		while ($objPattern->next())
		{
			$this->addDataRow($xml, $table, $objPattern->row(), $arrOrder);
			
			if (Pattern::isSubPattern($objPattern->type))
			{
				$this->addPatternData($xml, $table, $arrOrder, $objPattern->id);
			}
		}
	}
}
