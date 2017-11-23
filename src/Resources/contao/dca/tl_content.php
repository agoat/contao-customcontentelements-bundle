<?php

/*
 * Custom content elements extension for Contao Open Source CMS.
 *
 * @copyright  Arne Stappen (alias aGoat) 2017
 * @package    contao-customcontentelements
 * @author     Arne Stappen <mehh@agoat.xyz>
 * @link       https://agoat.xyz
 * @license    LGPL-3.0
 */


// Child table
$GLOBALS['TL_DCA']['tl_content']['config']['ctable'] = array('tl_data');

 
// Table callbacks
$GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][] = array('tl_content_elements', 'buildPaletteAndFields');
$GLOBALS['TL_DCA']['tl_content']['config']['onsubmit_callback'][] = array('tl_content_elements', 'saveData');

$GLOBALS['TL_DCA']['tl_content']['config']['oncreate_version_callback'][] = array('tl_content_elements', 'createDataVersion');
$GLOBALS['TL_DCA']['tl_content']['config']['onrestore_version_callback'][] = array('tl_content_elements', 'restoreDataVersion');

$GLOBALS['TL_DCA']['tl_content']['list']['sorting']['child_record_callback'] = array('tl_content_elements', 'addCteType');

// Remove some filter options
$GLOBALS['TL_DCA']['tl_content']['fields']['guests']['filter'] = false;

// Changes on the 'type' field
if (!\Config::get('disableVisualSelect'))
{
	$GLOBALS['TL_DCA']['tl_content']['fields']['type']['inputType'] = 'visualselect';
}
$GLOBALS['TL_DCA']['tl_content']['fields']['type']['eval']['helpwizard'] = false;
$GLOBALS['TL_DCA']['tl_content']['fields']['type']['xlabel']['helpwizard'] = array('tl_content_elements', 'addTypeHelpWizard');
$GLOBALS['TL_DCA']['tl_content']['fields']['type']['options_callback'] = array('tl_content_elements', 'getElements');
$GLOBALS['TL_DCA']['tl_content']['fields']['type']['load_callback'] = array(array('tl_content_elements', 'setDefaultType'));
$GLOBALS['TL_DCA']['tl_content']['fields']['type']['default'] = false;



/**
 * Provide methods that are used by the data configuration array.
 */	
class tl_content_elements extends tl_content
{
	/**
	 * Modified content data
	 * @var array
	 */
	protected $arrModifiedData = array();
	
	
	/**
	 * Wrap the content block elements into an div block and
	 * mark content element if content block is invisible
	 *
	 * @param array $arrRow
	 *
	 * @return string
	 */
	public function addCteType($arrRow)
	{	
		$return = \tl_content::addCteType($arrRow);
		
		if (($objElement = $objElement = \ElementsModel::findOneByAlias($arrRow['type'])) === null && !array_key_exists($arrRow['type'], $GLOBALS['TL_DCA']['tl_content']['palettes']))
		{
			$tag = '<span style="color: #222;">' . $GLOBALS['TL_LANG']['CTE']['deleted'] . '</span>';
		}
		else if ($objElement->invisible)
		{
			$tag = ' <span style="color: #c6c6c6;">(' . $GLOBALS['TL_LANG']['CTE']['invisible'] . ')</span>';
		}
		
		return ($tag) ? substr_replace($return, $tag . '</div>', strpos($return, '</div>')) : $return;
	}
	
	
	/**
	 * Add a custom help wirzard to the type field
	 *
	 * @param DataContainer $dc
	 *
	 * @return string
	 */
	public function addTypeHelpWizard ($dc)
	{
		return ' <a href="contao/help.php?table='.$dc->table.'&amp;field='.$dc->field.'&amp;ptable='.$dc->parentTable.'&amp;pid='.$dc->activeRecord->pid.'" title="' . \StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['helpWizard']) . '" onclick="Backend.openModalIframe({\'width\':735,\'title\':\''.\StringUtil::specialchars(str_replace("'", "\\'", $arrData['label'][0])).'\',\'url\':this.href});return false">'.\Image::getHtml('about.svg', $GLOBALS['TL_LANG']['MSC']['helpWizard'], 'style="vertical-align:text-bottom"').'</a>';
	}
	
	
	/**
	 * Generate a content element list array
	 *
	 * @param DataContainer $dc
	 *
	 * @return array
	 */
	public function getElements ($dc)
	{
		if ($dc->activeRecord !== null)
		{
			$ptable =  $dc->activeRecord->ptable;
			$pid = $dc->activeRecord->pid;
		}
		else
		{
			// If no activeRecord try to use get parameters (submitted by the custom help wizard)
			$ptable =  \Input::get('ptable');
			$pid = \Input::get('pid');
		}

		// Try to find content block elements for the theme
		$objLayout = \LayoutModel::findById(\Agoat\ContentElements\Controller::getLayoutId($ptable, $pid));	
		$colElements = \ElementsModel::findPublishedByPid($objLayout->pid);

		$arrElements = array();
		$strGroup = 'cte';
		
		if ($colElements !== null)
		{
			foreach ($colElements as $objElement)
			{
				if ($objElement->type == 'group')
				{
					$strGroup = $objElement->alias;
				}
				else
				{
					$arrElements[$strGroup][] = $objElement->alias;	
				}
			}
		}
		
		// Add standard content elements
		if (!\Config::get('hideLegacyCTE'))
		{
			unset($GLOBALS['TL_CTE']['CTE']);
			
			foreach ($GLOBALS['TL_CTE'] as $k=>$v)
			{
				foreach (array_keys($v) as $kk)
				{
					$arrElements[$k][] = $kk;
				}
			}
		}

		// Legacy support
		if ($dc->value != '' && !in_array($dc->value, array_reduce($arrElements, 'array_merge', array())))
		{
			if (($objLegacy = \ElementsModel::findOneByAlias($dc->value)) === null && !array_key_exists($dc->value, $GLOBALS['TL_DCA']['tl_content']['palettes']))
			{
				$arrLegacy = array('deleted' => array ($dc->value));
			}
			else if ($objLegacy->invisible)
			{
				$arrLegacy = array('invisible' => array ($dc->value));
			}
			else
			{
				$arrLegacy = array('legacy' => array ($dc->value));
			}	
			
			// Add current at top of the list
			$arrElements = array_merge($arrLegacy, $arrElements);
		}

		return $arrElements;
	}

	
	/**
	 * Set default value for new records
	 *
	 * @param mixed         @value
	 * @param DataContainer $dc
	 *
	 * @return mixed
	 */
	public function setDefaultType($value, $dc)
	{
		if (!$value)
		{
			if ($dc->activeRecord !== null)
			{
				$ptable =  $dc->activeRecord->ptable;
				$pid = $dc->activeRecord->pid;
			}
			else
			{
				return $value;
			}
			
			// Try to find content block elements for the theme
			$objLayout = \LayoutModel::findById(\Agoat\ContentElements\Controller::getLayoutId($ptable, $pid));	
			$objDefault = \ElementsModel::findDefaultPublishedElementByPid($objLayout->pid);
			
			// if no default content element is found 
			if ($objDefault === null)
			{
				if (\Config::get('hideLegacyCTE'))
				{
					$objDefault = \ElementsModel::findFirstPublishedElementByPid($objLayout->pid);
				}
				
				if ($objDefault === null)
				{
					$objDefault = new \stdClass();
					$objDefault->alias = 'text';
				}
			}

			$objContent = \ContentModel::findByPk($dc->id);

			$objContent->type = $objDefault->alias;
			$objContent->save();
				
			$this->redirect(\Environment::get('request'));
		}
	
		return $value;
	}

	
	/**
	 * Build the palette and field DCA for the virtual input fields
	 *
	 * @param DataContainer $dc
	 */
	public function buildPaletteAndFields ($dc)
	{
		// Build the content block elements palette and fields only when editing a content element (see #5)
		if (\Input::get('act') != 'edit' && \Input::get('act') != 'show' && !isset($_GET['target']))
		{
			return;
		}
		
		$intId = (isset($_GET['target'])) ? explode('.', \Input::get('target'))[2] : $dc->id;
		
		$objContent = \ContentModel::findByPk($intId);
	
		if ($objContent === null)
		{
			return;
		}

		// Get block element
		$objElement = \ElementsModel::findOneByAlias($objContent->type);
			
		if ($objElement === null)
		{
			return;
		}

		// Add default palette (for type selection)
		$GLOBALS['TL_DCA']['tl_content']['palettes'][$objElement->alias] = '{type_legend},type';

		// Add pattern to palette
		$colPattern = \PatternModel::findVisibleByPid($objElement->id);

		if ($colPattern === null)
		{
			return;
		}
		
		$arrData = array();

		$colData = \DataModel::findByPid($intId);
		
		if ($colData !== null)
		{
			foreach ($colData as $objData)
			{
				$arrData[$objData->pattern] = $objData;
			}							
		}
	
		foreach($colPattern as $objPattern)
		{
			// Construct dca for pattern
			$strClass = Agoat\CustomContentElementsBundle\Contao\Pattern::findClass($objPattern->type);
			$bolData = Agoat\CustomContentElementsBundle\Contao\Pattern::hasData($objPattern->type);
				
			if (!class_exists($strClass))
			{
				\System::log('Pattern element class "'.$strClass.'" (pattern element "'.$objPattern->type.'") does not exist', __METHOD__, TL_ERROR);
			}
			else
			{
				if ($bolData && !isset($arrData[$objPattern->alias]))
				{
					$arrData[$objPattern->alias] = new \DataModel();
					$arrData[$objPattern->alias]->pid = $objContent->id;
					$arrData[$objPattern->alias]->pattern = $objPattern->alias;
			
					$arrData[$objPattern->alias]->save();
				}
				
				$objPatternClass = new $strClass($objPattern);
				$objPatternClass->pid = $objContent->id;
				$objPatternClass->pattern = $objPattern->alias;
				$objPatternClass->element = $objElement->alias;			
				$objPatternClass->data = $arrData[$objPattern->alias]; // Save data to the DCA		

				$objPatternClass->create();
			}
		}
	}	

	
	/**
	 * Load the virtual field value from the DCA
	 *
	 * @param mixed         $value
	 * @param DataContainer $dc
	 *
	 * @return mixed|null
	 */
	public function loadFieldValue ($value, $dc)
	{
		if (!empty($value))
		{
			return $value;
		}

		if (!empty($GLOBALS['TL_DCA']['tl_content']['fields'][$dc->field]['data']))
		{
			return $GLOBALS['TL_DCA']['tl_content']['fields'][$dc->field]['data'];
		}
		
		return null;
	}

	
	/**
	 * Save the virtual field values into the tl_data table
	 *
	 * @param DataContainer $dc
	 */
	public function saveData (&$dc)
	{
		// Save all modified values
		foreach ($this->arrModifiedData as $field => $value)
		{
			// Get virtual field attributes from DCA
			$id = $GLOBALS['TL_DCA']['tl_content']['fields'][$field]['id'];
			$pattern = $GLOBALS['TL_DCA']['tl_content']['fields'][$field]['pattern'];
			$parent = $GLOBALS['TL_DCA']['tl_content']['fields'][$field]['parent'];
			$column = $GLOBALS['TL_DCA']['tl_content']['fields'][$field]['column'];

			if ($id !== null)
			{
				$objData = \DataModel::findByPK($id);
			}
			else
			{
				$objData = \DataModel::findByPidAndPatternAndParent($dc->activeRecord->id, $pattern, $parent);
			}

			if ($objData === null)
			{
				// if no dataset exist make a new one
				$objData = new \DataModel();
				$objData->tstamp = time();
			}

			$objData->pid = $dc->activeRecord->id;
			$objData->pattern = $pattern;
			$objData->parent = $parent;
		
			if ($objData->$column != $value)
			{
				$dc->blnCreateNewVersion = true;
				$objData->$column = $value;
				$objData->tstamp = time();
			}

			$objData->save();
		}
	}


	/**
	 * Save the virtual field value to the modified data array and return null to prevent saving to the database
	 *
	 * @param mixed         $value
	 * @param DataContainer $dc
	 *
	 * @return null
	 */
	public function saveFieldAndClear ($value, $dc)
	{
		$this->arrModifiedData[$dc->field] = $value;

		return null;
	}

	
	/**
	 * Prepare the virtual order field
	 *
	 * @param mixed         $value
	 * @param DataContainer $dc
	 *
	 * @return mixed
	 */
	public function prepareOrderValue ($value, $dc)
	{
		$orderField = $GLOBALS['TL_DCA']['tl_content']['fields'][$dc->field]['eval']['orderField'];
		$orderData = \StringUtil::deserialize($GLOBALS['TL_DCA']['tl_content']['fields'][$orderField]['data']);
		
		$GLOBALS['TL_DCA']['tl_content']['fields'][$dc->field]['eval'][$orderField] = (is_array($orderData)) ? $orderData : array();

		return $value;
	}

	
	/**
	 * Save the virtual order field
	 *
	 * @param mixed         $value
	 * @param DataContainer $dc
	 *
	 * @return mixed
	 */
	public function saveOrderValue ($value, $dc)
	{
		$orderField = $GLOBALS['TL_DCA']['tl_content']['fields'][$dc->field]['eval']['orderField'];

		$orderData = \Input::post($orderField);
	
		if ($orderData)
		{
			// Convert UUID to binary data for file sources
			if ($GLOBALS['TL_DCA']['tl_content']['fields'][$dc->field]['inputType'] == 'fileTree')
			{
				$this->arrModifiedData[$orderField] = array_map('\StringUtil::uuidToBin', explode(',', $orderData));
			}
			else
			{
				$this->arrModifiedData[$orderField] = explode(',', $orderData);
			}
		}
		else
		{
			$this->arrModifiedData[$orderField] = NULL;
		}

		return $value;
	}

	
	
	/**
	 * Dynamically set the ace highlight syntax
	 *
	 * @param mixed         $value
	 * @param DataContainer $dc
	 *
	 * @return mixed
	 */
	public function setAceCodeHighlighting($value, $dc)
	{
		$pattern = $GLOBALS['TL_DCA']['tl_content']['fields'][$dc->field]['pattern'];
		$highlight = $GLOBALS['TL_DCA']['tl_content']['fields'][$pattern . '-highlight']['data'];

		if (!empty($highlight))
		{
			$GLOBALS['TL_DCA']['tl_content']['fields'][$dc->field]['eval']['rte'] = 'ace|' . strtolower($highlight);
		}

		return $value;
	}
	

	/**
	 * Set default value for virtual fields in new records
	 *
	 * @param mixed         $value
	 * @param DataContainer $dc
	 *
	 * @return mixed
	 */
	public function defaultValue($value, $dc)
	{
		if ($value == '')
		{			
			return $GLOBALS['TL_DCA']['tl_content']['fields'][$dc->field]['default'];
		}

		return $value;
	}

	/**
	 * Save tl_data versions
	 *
	 * @param string        $strTable
	 * @param integer       $intPid
	 * @param integer       $intVersion
	 */
	public function createDataVersion ($strTable, $intPid, $intVersion)
	{
		$db = Database::getInstance();
		
		$objData = $db->prepare("SELECT * FROM tl_data WHERE pid=?")
					  ->execute($intPid);	
			  
		if ($objData === null)
		{
			return;
		}

		$db->prepare("INSERT INTO tl_version (pid, tstamp, version, fromTable, data) VALUES (?, ?, ?, ?, ?)")
		   ->execute($intPid, time(), $intVersion, 'tl_data', serialize($objData->fetchAllAssoc()));
	}
	
	
	/**
	 * Restore tl_data versions
	 *
	 * @param string        $strTable
	 * @param integer       $intPid
	 * @param integer       $intVersion
	 */
	public function restoreDataVersion ($strTable, $intPid, $intVersion)
	{
		$db = Database::getInstance();
		
		$objData = $db->prepare("SELECT * FROM tl_version WHERE fromTable=? AND pid=? AND version=?")
					  ->execute('tl_data', $intPid, $intVersion);

		if ($objData->numRows < 1)
		{
			return;
		}
		
		$data = \StringUtil::deserialize($objData->data);
	
		if (!is_array($data))
		{
			return;
		}
		
		// Get the currently available fields
		$arrFields = array_flip($this->Database->getFieldnames('tl_data'));

		$objStmt = $db->prepare("DELETE FROM tl_data WHERE pid=?")
					  ->execute($intPid);

		foreach ($data as $row)
		{
			// Unset fields that do not exist (see #5219)
			$row = array_intersect_key($row, $arrFields);
				
			// Reset fields added after storing the version to their default value (see #7755)
			foreach (array_diff_key($arrFields, $row) as $k=>$v)
			{
				$row[$k] = \Widget::getEmptyValueByFieldType($GLOBALS['TL_DCA']['tl_data']['fields'][$k]['sql']);
			}
			
			$db->prepare("INSERT INTO tl_data %s")
			   ->set($row)
			   ->execute($row['id']);
		}
	}
}
