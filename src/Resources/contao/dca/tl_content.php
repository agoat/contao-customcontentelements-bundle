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


// Child table
$GLOBALS['TL_DCA']['tl_content']['config']['ctable'] = array('tl_data');

 
// Table callbacks
$GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][] = array('tl_content_elements', 'buildPaletteAndFields');
$GLOBALS['TL_DCA']['tl_content']['config']['onsubmit_callback'][] = array('tl_content_elements', 'saveData');

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



	
class tl_content_elements extends tl_content
{
	/**
	 * @var array returned field Data
	 *
	 * array[patternAlias][parentID][columnName]
	 */
	protected $arrLoadedData = array();

	/**
	 * @var array returned field Data
	 */
	protected $arrModifiedData = array();

	/**
	 * @var string default content element type
	 */
	protected $strDefaultType = '';
	
	
	/**
	 * Wrap the content block elements into an div block and
	 * mark content element if content block is invisible
	 *
	 * @param array $arrRow
	 * @return string
	 *
	 */
	public function addCteType($arrRow)
	{	
		$return = \tl_content::addCteType($arrRow);
		
		$objElement = \ElementsModel::findOneByAlias($arrRow['type']);
	
		if ($objElement === null)
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
	 */
	public function addTypeHelpWizard ($dc)
	{
		return ' <a href="contao/help.php?table='.$dc->table.'&amp;field='.$dc->field.'&amp;ptable='.$dc->parentTable.'&amp;pid='.$dc->activeRecord->pid.'" title="' . \StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['helpWizard']) . '" onclick="Backend.openModalIframe({\'width\':735,\'title\':\''.\StringUtil::specialchars(str_replace("'", "\\'", $arrData['label'][0])).'\',\'url\':this.href});return false">'.\Image::getHtml('about.svg', $GLOBALS['TL_LANG']['MSC']['helpWizard'], 'style="vertical-align:text-bottom"').'</a>';
	}
	
	
	/**
	 * Generate content element list for type selection
	 *
	 * @param object $dc The DataContainer object
	 *
	 * @return array The content elements
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
		$strGroup = 'ctb';
		
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
			unset($GLOBALS['TL_CTE']['CTB']);
			
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
			$objLegacy = \ElementsModel::findOneByAlias($dc->value);
			
			if ($objLegacy === null)
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
	 * set default value for new records
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
				else
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
	 * Build palette and field DCA
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
		
		$colData = \DataModel::findByPid($intId);
		
		if ($colData !== null)
		{
			foreach ($colData as $objData)
			{
				$arrData[$objData->pattern] = $objData->row();
			}							
		}
		else
		{
			$arrData = null;
		}
		
		foreach($colPattern as $objPattern)
		{
			// construct dca for pattern
			$strClass = \Agoat\ContentElements\Pattern::findClass($objPattern->type);
				
			if (!class_exists($strClass))
			{
				\System::log('Pattern element class "'.$strClass.'" (pattern element "'.$objPattern->type.'") does not exist', __METHOD__, TL_ERROR);
			}
			else
			{
				$objPatternClass = new $strClass($objPattern);
				$objPatternClass->pid = $objContent->id;
				$objPatternClass->pattern = $objPattern->alias;
				$objPatternClass->alias = $objElement->alias;			
				$objPatternClass->data = (isset($arrData[$objPattern->alias])) ? $arrData[$objPattern->alias] : null;			

				$objPatternClass->construct();
			}
		}
	}	

	
	/**
	 * load field value from tl_content_value table
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
		
		return $value;

	}

	
	/**
	 * save field Data to tl_content_value table
	 */
	public function saveData (&$dc)
	{
		// Save all modified values
		foreach ($this->arrModifiedData as $field => $value)
		{
			// Get virtual field attributes from DCA
			$pattern = $GLOBALS['TL_DCA']['tl_content']['fields'][$field]['pattern'];
			$parent = $GLOBALS['TL_DCA']['tl_content']['fields'][$field]['parent'];
			$column = $GLOBALS['TL_DCA']['tl_content']['fields'][$field]['column'];

			$objData = \DataModel::findOneByPidAndPatternAndParent($dc->activeRecord->id, $pattern, $parent);

			if ($objData === null)
			{
				// if no dataset exist make a new one
				$objData = new \DataModel();
			}

			$objData->pid = $dc->activeRecord->id;
			$objData->pattern = $pattern;
			$objData->parent = $parent;
			$objData->tstamp = time();
		
			if ($objData->$column != $value)
			{
				$dc->blnCreateNewVersion = true;
				$objData->$column = $value;
			}
		
			$objData->save();
		}
	}


	/**
	 * save field value to tl_content_value table
	 */
	public function saveFieldAndClear ($value, $dc)
	{
		$this->arrModifiedData[$dc->field] = $value;
		
		return null;
	}

	
	/**
	 * prepare the virtual order field
	 */
	public function prepareOrderValue ($value, $dc)
	{
		$orderField = $GLOBALS['TL_DCA']['tl_content']['fields'][$dc->field]['eval']['orderField'];
		$orderData = \StringUtil::deserialize($GLOBALS['TL_DCA']['tl_content']['fields'][$orderField]['data']);
		
		$GLOBALS['TL_DCA']['tl_content']['fields'][$dc->field]['eval'][$orderField] = (is_array($orderData)) ? $orderData : array();

		return $value;
	}

	/**
	 * save the virtual order field
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
	 * Dynamically set the ace syntax
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
	 * set default value for new records
	 */
	public function defaultValue($value, $dc)
	{
		if ($value == '')
		{			
			return $GLOBALS['TL_DCA']['tl_content']['fields'][$dc->field]['default'];
		}

		return $value;
	}

}
