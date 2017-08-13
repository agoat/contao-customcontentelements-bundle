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



// Table callbacks
$GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][] = array('tl_content_elements', 'buildPaletteAndFields');
$GLOBALS['TL_DCA']['tl_content']['config']['onsubmit_callback'][] = array('tl_content_elements', 'saveFieldData');

$GLOBALS['TL_DCA']['tl_content']['config']['oncopy_callback'][] = array('tl_content_elements', 'copyRelatedData');
$GLOBALS['TL_DCA']['tl_content']['config']['ondelete_callback'][] = array('tl_content_elements', 'deleteRelatedData');

$GLOBALS['TL_DCA']['tl_content']['config']['oncreate_version_callback'][] = array('tl_content_elements', 'createRelatedDataVersion');
$GLOBALS['TL_DCA']['tl_content']['config']['onrestore_version_callback'][] = array('tl_content_elements', 'restoreRelatedDataVersion');

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
	 * array[duplicatId][patternId][columnName]
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
		// get block element
		$objBlock = \ElementsModel::findOneByAlias($arrRow['type']);
		
		$return = \tl_content::addCteType($arrRow);
		return ($objBlock->invisible) ? substr_replace($return, ' <span style="color: #b3b3b3;">(invisible content block)</div>', strpos($return, '</div>')) : $return;

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
			return array($dc->value);
		}
	
		return $arrElements;
	}


	/**
	 * Dynamically set the ace syntax
	 */
	public function setAceCodeHighlighting($value, $dc)
	{
		$id = explode('-', $dc->field);
		if (!empty($this->arrLoadedData[$id[1]][$id[2]]['highlight']))
		{
			$GLOBALS['TL_DCA']['tl_content']['fields'][$dc->field]['eval']['rte'] = 'ace|' . strtolower($this->arrLoadedData[$id[1]][$id[2]]['highlight']);
		}

		return $value;
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
	 * build palettes and field DCA and load Data from tl_content_value
	 */
	public function buildPaletteAndFields ($dc)
	{
		// build the content block elements palette and fields only when editing a content element (see #5)
		if (\Input::get('act') != 'edit' && \Input::get('act') != 'show' && !isset($_GET['target']))
		{
			return;
		}
		
		$intId = (isset($_GET['target'])) ? explode('.', \Input::get('target'))[2] : $dc->id;
		
		// get content element
		$objContent = \ContentModel::findByPk($intId);
	
		if ($objContent === null)
		{
			return;
		}

		// get block element
		$objBlock = \ElementsModel::findOneByAlias($objContent->type);
			
		if ($objBlock === null)
		{
			return;
		}

		// add default palette (for type selection)
		$GLOBALS['TL_DCA']['tl_content']['palettes'][$objBlock->alias] = '{type_legend},type';

					
		// add the pattern to palettes
		$colPattern = \PatternModel::findPublishedByPidAndTable($objBlock->id, 'tl_elements', array('order'=>'sorting ASC'));

		if ($colPattern === null)
		{
			return;
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
				$objPatternClass->cid = $objContent->id;
				$objPatternClass->rid = 0;
				$objPatternClass->alias = $objBlock->alias;			

				$objPatternClass->construct();
			}
		}
	}	

	
	/**
	 * save field Data to tl_content_value table
	 */
	public function saveFieldData (&$dc)
	{
		foreach ($this->arrModifiedData as $pid => $pattern)
		{
			foreach ($pattern as $rid => $fields)
			{
				$bolVersion = false;
				$objData = \DataModel::findByCidandPidandRid($dc->activeRecord->id,$pid,$rid);
			
				if ($objData === null)
				{
					// if no dataset exist make a new one
					$objData = new \DataModel();
				}
			
				$objData->cid = $dc->activeRecord->id;
				$objData->pid = $pid;
				$objData->rid = $rid;
				$objData->tstamp = time();
			
				foreach ($fields as $k=>$v)
				{
					if ($objData->$k != $v)
					{
						$bolVersion = true;
						$objData->$k = $v;
					}
				}
				
				$objData->save();
				
				if ($bolVersion)
				{
					$dc->blnCreateNewVersion = true;
				}
			}
		}
	}

	
	/**
	 * delete related Datas when a content element is deleted
	 */
	public function deleteRelatedData ($dc, $intUndoId)
	{
		$db = Database::getInstance();
		
		// get the undo database row
		$objUndo = $db->prepare("SELECT data FROM tl_undo WHERE id=?")
					  ->execute($intUndoId) ;
			
		$arrData = \StringUtil::deserialize($objUndo->fetchAssoc()[data]);
		
		$colData = \DataModel::findByCid($dc->activeRecord->id);
		
		if ($colData === null)
		{
			return;
		}

		foreach ($colData as $objData)
		{
			// get Data row(s)
			$arrData['tl_data'][] = $objData->row();

			$objData->delete();
		}
	
		// save back to the undo database row
		$db->prepare("UPDATE tl_undo SET data=? WHERE id=?")
		   ->execute(serialize($arrData), $intUndoId);
	}

	
	/**
	 * copy related Data when a content element is copied
	 */
	public function copyRelatedData ($intId, $dc)
	{
		$colData = \DataModel::findByCid($dc->id);

		if ($colData === null)
		{
			return;
		}
		
		foreach ($colData as $objValue)
		{
			$objNewValue = clone $objValue;
			$objNewValue->cid = $intId;
			$objNewValue->save();
		} 
	}

	/**
	 * save new version with the content element for each pattern value
	 */
	public function createRelatedDataVersion ($strTable, $intPid, $intVersion, $row)
	{
		$db = Database::getInstance();
		
		// get tl_content_Data collection
		$colData = \DataModel::findByCid($intPid);
	
		if ($colData === null)
		{
			return;
		}
		
		$this->import('BackendUser', 'User');
		
		foreach ($colData as $objData)
		{
			$db->prepare("UPDATE tl_version SET active='' WHERE pid=? AND fromTable=?")
			   ->execute($objData->id, 'tl_data');
				   
			$db->prepare("INSERT INTO tl_version (pid, tstamp, version, fromTable, username, userid, active, data) VALUES (?, ?, ?, ?, ?, ?, 1, ?)")
			   ->execute($objData->id, time(), $intVersion, 'tl_data', $this->User->username, $this->User->id, serialize($objData->row()));
		} 			
		
	}

	/**
	 * restore Data for each pattern value with the content element 
	 */
	public function restoreRelatedDataVersion ($strTable, $intPid, $intVersion, $data)
	{
		$db = Database::getInstance();
		
		// get tl_content_Data collection
		$colData = \DataModel::findByCid($intPid);
	
		if ($colData === null)
		{
			return; 
		}

		foreach ($colData as $objData)
		{

			// get Data version (don´t use the version class because the callback is inside the restore method)
			$objVersion = $db->prepare("SELECT * FROM tl_version WHERE fromTable=? AND pid=? AND version=?")
							 ->limit(1)
							 ->execute('tl_data', $objData->id, $intVersion);
	
			if ($objVersion->numRows < 1)
			{
				break;
			}
			
			$data = \StringUtil::deserialize($objVersion->data);
			
			if (!is_array($data))
			{
				break;
			}
	
				
			// Get the currently available fields
			$arrFields = array_flip($this->Database->getFieldnames('tl_data'));
			
			// Unset fields that do not exist (see #5219)
			$data = array_intersect_key($data, $arrFields);
				
			$this->loadDataContainer('tl_content_value');
	
			// Reset fields added after storing the version to their default value (see #7755)
			foreach (array_diff_key($arrFields, $data) as $k=>$v)
			{
				$data[$k] = \Widget::getEmptyValueByFieldType($GLOBALS['TL_DCA']['tl_data']['fields'][$k]['sql']);
			}
		
			
			$db->prepare("UPDATE tl_data %s WHERE id=?")
			   ->set($data)
			   ->execute($objData->id);
			
			$db->prepare("UPDATE tl_version SET active='' WHERE fromTable=? AND pid=?")
			   ->execute('tl_data', $objData->id);
			
			$db->prepare("UPDATE tl_version SET active=1 WHERE fromTable=? AND pid=? AND version=?")
			   ->execute('tl_data', $objData->id, $intVersion);
			
			
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

		$this->loadContentData($dc->id);

		$id = explode('-', $dc->field);	

		if (!empty($this->arrLoadedData[$id[1]][$id[2]][$id[0]]))
		{
			return $this->arrLoadedData[$id[1]][$id[2]][$id[0]];
		}
		
		return $value;
	}

	/**
	 * save field value to tl_content_value table
	 */
	public function saveFieldAndClear ($value, $dc)
	{
		$id = explode('-', $dc->field);
		$this->arrModifiedData[$id[1]][$id[2]][$id[0]] = $value;

		return null;
	}


	
	/**
	 * prepare the virtual orderSRC field (filetree widget)
	 */
	public function prepareOrderSRCValue ($value, $dc)
	{
		$this->loadContentData($dc->id);
		
		// Prepare the order field
		$id = explode('-', $dc->field);
		$orderSRC = \StringUtil::deserialize($this->arrLoadedData[$id[1]][$id[2]]['orderSRC']);
		$GLOBALS['TL_DCA']['tl_content']['fields'][$dc->field]['eval']['orderSRC-'.$id[1].'-'.$id[2]] = (is_array($orderSRC)) ? $orderSRC : array();

		return $value;
	}

	/**
	 * save the virtual orderSRC field (filetree widget)
	 */
	public function saveOrderSRCValue ($value, $dc)
	{
		// Prepare the order field
		$id = explode('-', $dc->field);
		$orderSRC = (\Input::post('orderSRC-'.$id[1].'-'.$id[2])) ? array_map('\StringUtil::uuidToBin', explode(',', \Input::post('orderSRC-'.$id[1].'-'.$id[2]))) : false;
		$this->arrModifiedData[$id[1]][$id[2]]['orderSRC'] = $orderSRC;
		
		return $value;
	}

	/**
	 * prepare the virtual orderPage field (pagetree widget)
	 */
	public function prepareOrderPageValue ($value, $dc)
	{
		$this->loadContentData($dc->id);

		// Prepare the order field
		$id = explode('-', $dc->field);
		$orderPage = \StringUtil::deserialize($this->arrLoadedData[$id[1]][$id[2]]['orderPage']);
		$GLOBALS['TL_DCA']['tl_content']['fields'][$dc->field]['eval']['orderPage-'.$id[1].'-'.$id[2]] = (is_array($orderPage)) ? $orderPage : array();

		return $value;
	}

	/**
	 * save the virtual orderPage field (pagetree widget)
	 */
	public function saveOrderPageValue ($value, $dc)
	{
		// Prepare the order field
		$id = explode('-', $dc->field);
		$orderPage = (\Input::post('orderPage-'.$id[1].'-'.$id[2])) ? explode(',', \Input::post('orderPage-'.$id[1].'-'.$id[2])) : false;
		$this->arrModifiedData[$id[1]][$id[2]]['orderPage'] = $orderPage;
		
		return $value;
	}
	
	/**
	 * load the content Data from the tl_content_value database
	 */
	protected function loadContentData ($intId)
	{
		if (empty($this->arrLoadedData))
		{
			$colValue = \DataModel::findByCid($intId);
			
			if ($colValue !== null)
			{
				foreach ($colValue as $objValue)
				{
					$this->arrLoadedData[$objValue->pid][$objValue->rid] = $objValue->row();
				}							
			}
		}
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
