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
$GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][] = array('tl_content_contentblocks', 'buildPaletteAndFields');
$GLOBALS['TL_DCA']['tl_content']['config']['onsubmit_callback'][] = array('tl_content_contentblocks', 'saveFieldValues');

$GLOBALS['TL_DCA']['tl_content']['config']['oncopy_callback'][] = array('tl_content_contentblocks', 'copyRelatedValues');
$GLOBALS['TL_DCA']['tl_content']['config']['ondelete_callback'][] = array('tl_content_contentblocks', 'deleteRelatedValues');

$GLOBALS['TL_DCA']['tl_content']['config']['oncreate_version_callback'][] = array('tl_content_contentblocks', 'createRelatedValuesVersion');
$GLOBALS['TL_DCA']['tl_content']['config']['onrestore_version_callback'][] = array('tl_content_contentblocks', 'restoreRelatedValuesVersion');

$GLOBALS['TL_DCA']['tl_content']['list']['sorting']['child_record_callback'] = array('tl_content_contentblocks', 'addCteType');

// Remove some filter options
$GLOBALS['TL_DCA']['tl_content']['fields']['guests']['filter'] = false;


// Changes on the 'type' field
if (!\Config::get('disableVisualSelect'))
{
	$GLOBALS['TL_DCA']['tl_content']['fields']['type']['inputType'] = 'visualselect';
}
$GLOBALS['TL_DCA']['tl_content']['fields']['type']['eval']['helpwizard'] = false;
$GLOBALS['TL_DCA']['tl_content']['fields']['type']['xlabel']['helpwizard'] = array('tl_content_contentblocks', 'addTypeHelpWizard');
$GLOBALS['TL_DCA']['tl_content']['fields']['type']['options_callback'] = array('tl_content_contentblocks', 'getContentBlockElements');
$GLOBALS['TL_DCA']['tl_content']['fields']['type']['load_callback'] = array(array('tl_content_contentblocks', 'setDefaultType'));
$GLOBALS['TL_DCA']['tl_content']['fields']['type']['default'] = false;



	
class tl_content_contentblocks extends tl_content
{
	/**
	 * @var array returned field values
	 *
	 * array[duplicatId][patternId][columnName]
	 */
	protected $arrLoadedValues = array();

	/**
	 * @var array returned field values
	 */
	protected $arrModifiedValues = array();
	
	
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
		$objBlock = \ContentBlocksModel::findOneByAlias($arrRow['type']);
		
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
	public function getContentBlockElements ($dc)
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
		$objLayout = \LayoutModel::findById(\Agoat\ContentBlocks\Controller::getLayoutId($ptable, $pid));	
		$colContentBlocks = \ContentBlocksModel::findPublishedByPid($objLayout->pid);

		$arrElements = array();
		$strGroup = 'ctb';
		
		if ($colContentBlocks !== null)
		{
			foreach ($colContentBlocks as $objCB)
			{
				if ($objCB->type == 'group')
				{
					$strGroup = $objCB->alias;
				}
				else
				{
					$arrElements[$strGroup][] = $objCB->alias;					
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
dump($arrElements);		
		return $arrElements;
	}


	/**
	 * Dynamically set the ace syntax
	 */
	public function setAceCodeHighlighting($value, $dc)
	{
		$id = explode('-', $dc->field);
		if (!empty($this->arrLoadedValues[$id[1]][$id[2]]['highlight']))
		{
			$GLOBALS['TL_DCA']['tl_content']['fields'][$dc->field]['eval']['rte'] = 'ace|' . strtolower($this->arrLoadedValues[$id[1]][$id[2]]['highlight']);
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
			// try to get the theme id
			$intLayoutId = \Agoat\ContentBlocks\Controller::getLayoutId($dc->activeRecord->ptable,  $dc->activeRecord->pid);
	
			$objLayout = \LayoutModel::findById($intLayoutId);

			// try to find default content block element
			if (is_array($GLOBALS['TL_CTB_DEFAULT']) && $objLayout)
			{
				$strDefaultCTB = $GLOBALS['TL_CTB_DEFAULT'][$objLayout->pid];
			}
		
			// if no default content block is found 
			if (!$strDefaultCTB)
			{
				if (\Config::get('overwriteCTE'))
				{
					return ''; // return and do not save and redirect (results in endless redirection)
					
				}
				else
				{
					$strDefaultCTB = 'text';
				}
			}

			$objContent = \ContentModel::findByPk($dc->id);

			$objContent->type = $strDefaultCTB;
			$objContent->save();
				
			$this->redirect(\Environment::get('request'));
		}
	
		return $value;
	}

	
	/**
	 * build palettes and field DCA and load values from tl_content_value
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
		$objBlock = \ContentBlocksModel::findOneByAlias($objContent->type);
			
		if ($objBlock === null)
		{
			return;
		}

		// add default palette (for type selection)
		$GLOBALS['TL_DCA']['tl_content']['palettes'][$objBlock->alias] = '{type_legend},type';

					
		// add the pattern to palettes
		$colPattern = \ContentPatternModel::findPublishedByPidAndTable($objBlock->id, 'tl_content_blocks', array('order'=>'sorting ASC'));

		if ($colPattern === null)
		{
			return;
		}

		foreach($colPattern as $objPattern)
		{
			// construct dca for pattern
			$strClass = \Agoat\ContentBlocks\Pattern::findClass($objPattern->type);
				
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
	 * save field values to tl_content_value table
	 */
	public function saveFieldValues (&$dc)
	{
		foreach ($this->arrModifiedValues as $pid => $pattern)
		{
			foreach ($pattern as $rid => $fields)
			{
				$bolVersion = false;
				$objValue = \ContentValueModel::findByCidandPidandRid($dc->activeRecord->id,$pid,$rid);
				if ($objValue === null)
				{
					// if no dataset exist make a new one
					$objValue = new ContentValueModel();
				}
				
				$objValue->cid = $dc->activeRecord->id;
				$objValue->pid = $pid;
				$objValue->rid = $rid;
				$objValue->tstamp = time();
			
				foreach ($fields as $k=>$v)
				{
					
					if ($objValue->$k != $v)
					{
						$bolVersion = true;
						$objValue->$k = $v;
					}
				}
				
				$objValue->save();
				
				if ($bolVersion)
				{
					$dc->blnCreateNewVersion = true;
				}
				
			}
		}
	}

	
	/**
	 * delete related Values when a content element is deleted
	 */
	public function deleteRelatedValues ($dc, $intUndoId)
	{
		$db = Database::getInstance();
		
		// get the undo database row
		$objUndo = $db->prepare("SELECT data FROM tl_undo WHERE id=?")
					  ->execute($intUndoId) ;
			
		$arrData = \StringUtil::deserialize($objUndo->fetchAssoc()[data]);
		
		$colValues = \ContentValueModel::findByCid($dc->activeRecord->id);
		
		if ($colValues === null)
		{
			return;
		}

		foreach ($colValues as $objValue)
		{
			// get value row(s)
			$arrData['tl_content_value'][] = $objValue->row();

			$objValue->delete();
		}
	
		// save back to the undo database row
		$db->prepare("UPDATE tl_undo SET data=? WHERE id=?")
		   ->execute(serialize($arrData), $intUndoId);
	}

	
	/**
	 * copy related Values when a content element is copied
	 */
	public function copyRelatedValues ($intId, $dc)
	{
		$colValues = \ContentValueModel::findByCid($dc->id);

		if ($colValues === null)
		{
			return;
		}
		
		foreach ($colValues as $objValue)
		{
			$objNewValue = clone $objValue;
			$objNewValue->cid = $intId;
			$objNewValue->save();
		} 
	}

	/**
	 * save new version with the content element for each pattern value
	 */
	public function createRelatedValuesVersion ($strTable, $intPid, $intVersion, $row)
	{
		$db = Database::getInstance();
		
		// get tl_content_values collection
		$colValues = \ContentValueModel::findByCid($intPid);

		if ($colValues === null)
		{
			return;
		}
		
		$this->import('BackendUser', 'User');
		
		foreach ($colValues as $objValue)
		{
			$db->prepare("UPDATE tl_version SET active='' WHERE pid=? AND fromTable=?")
			   ->execute($objValue->id, 'tl_content_value');
					   
			$db->prepare("INSERT INTO tl_version (pid, tstamp, version, fromTable, username, userid, description, editUrl, active, data) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?)")
			   ->execute($objValue->id, time(), $intVersion, 'tl_content_value', $this->User->username, $this->User->id, '', '', serialize($objValue->row()));

		} 			
		
	}

	/**
	 * restore values for each pattern value with the content element 
	 */
	public function restoreRelatedValuesVersion ($strTable, $intPid, $intVersion, $data)
	{
		$db = Database::getInstance();
		
		// get tl_content_values collection
		$colValues = \ContentValueModel::findByCid($intPid);
	
		if ($colValues === null)
		{
			return; 
		}

		foreach ($colValues as $objValue)
		{

			// get values version (don´t use the version class because the callback is inside the restore method)
			$objData = $this->Database->prepare("SELECT * FROM tl_version WHERE fromTable=? AND pid=? AND version=?")
									  ->limit(1)
									  ->execute('tl_content_value', $objValue->id, $intVersion);
	
			if ($objData->numRows < 1)
			{
				break;
			}
			
			$data = \StringUtil::deserialize($objData->data);
			
			if (!is_array($data))
			{
				break;
			}
	
				
			// Get the currently available fields
			$arrFields = array_flip($this->Database->getFieldnames('tl_content_value'));
			
			// Unset fields that do not exist (see #5219)
			$data = array_intersect_key($data, $arrFields);
				
			$this->loadDataContainer('tl_content_value');
	
			// Reset fields added after storing the version to their default value (see #7755)
			foreach (array_diff_key($arrFields, $data) as $k=>$v)
			{
				$data[$k] = \Widget::getEmptyValueByFieldType($GLOBALS['TL_DCA']['tl_content_value']['fields'][$k]['sql']);
			}
		
			
			$db->prepare("UPDATE tl_content_value %s WHERE id=?")
			   ->set($data)
			   ->execute($objValue->id);
			
			$db->prepare("UPDATE tl_version SET active='' WHERE fromTable=? AND pid=?")
			   ->execute('tl_content_value', $objValue->id);
			
			$db->prepare("UPDATE tl_version SET active=1 WHERE fromTable=? AND pid=? AND version=?")
			   ->execute('tl_content_value', $objValue->id, $intVersion);
			
			
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

		$this->loadContentValues($dc->id);

		$id = explode('-', $dc->field);	

		if (!empty($this->arrLoadedValues[$id[1]][$id[2]][$id[0]]))
		{
			return $this->arrLoadedValues[$id[1]][$id[2]][$id[0]];
		}
		
		return $value;
	}

	/**
	 * save field value to tl_content_value table
	 */
	public function saveFieldAndClear ($value, $dc)
	{
		$id = explode('-', $dc->field);
		$this->arrModifiedValues[$id[1]][$id[2]][$id[0]] = $value;

		return null;
	}


	
	/**
	 * prepare the virtual orderSRC field (filetree widget)
	 */
	public function prepareOrderSRCValue ($value, $dc)
	{
		$this->loadContentValues($dc->id);
		
		// Prepare the order field
		$id = explode('-', $dc->field);
		$orderSRC = \StringUtil::deserialize($this->arrLoadedValues[$id[1]][$id[2]]['orderSRC']);
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
		$this->arrModifiedValues[$id[1]][$id[2]]['orderSRC'] = $orderSRC;
		
		return $value;
	}

	/**
	 * prepare the virtual orderPage field (pagetree widget)
	 */
	public function prepareOrderPageValue ($value, $dc)
	{
		$this->loadContentValues($dc->id);

		// Prepare the order field
		$id = explode('-', $dc->field);
		$orderPage = \StringUtil::deserialize($this->arrLoadedValues[$id[1]][$id[2]]['orderPage']);
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
		$this->arrModifiedValues[$id[1]][$id[2]]['orderPage'] = $orderPage;
		
		return $value;
	}
	
	/**
	 * load the content values from the tl_content_value database
	 */
	protected function loadContentValues ($intId)
	{
		if (empty($this->arrLoadedValues))
		{
			$colValue = \ContentValueModel::findByCid($intId);
			
			if ($colValue !== null)
			{
				foreach ($colValue as $objValue)
				{
					$this->arrLoadedValues[$objValue->pid][$objValue->rid] = $objValue->row();
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
