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


namespace Contao;


/**
 * Provide methods to show an explanation 
 *
 * @property array   $options
 * @property boolean $multiple
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class MultiGroup extends \Widget
{

	/**
	 * Submit user input
	 * @var boolean
	 */
	protected $blnSubmitInput = true;

	
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_widget_rdo';



	/**
	 * Trim values
	 *
	 * @param mixed $varInput
	 *
	 * @return mixed
	 */
	protected function validator($varInput)
	{
		if ($varInput > $this->numberOfGroups)
		{
			$varInput = $this->numberOfGroups;
		}
		else if ($varInput < 1)
		{
			$varInput = 1;
		}
		
		return parent::validator($varInput);
	}

	
	
	/**
	 * Generate the widget and return it as string
	 *
	 * @return string
	 */
	public function generate()
	{
		// Change pattern content values 
		if (\Input::get($this->strCommand) && is_numeric(\Input::get('cid')) && \Input::get('id') == $this->currentRecord)
		{
			// Set the value at least to 1
			if ($this->varValue < 1)
			{
				$this->varValue = 1;
			}
			
			// Let the DC_Table first save the post values 
			if (\Environment::get('request_method') == 'GET')
			{
				// Correct and save group counter
				switch(\Input::get($this->strCommand))
				{
					case 'insert':
						if ($this->varValue < $this->numberOfGroups)
						{
							$objValue = \ContentValueModel::findByCidandPidandRid($this->currentRecord, $this->pid, $this->rid);
							
							if ($objValue === null)
							{
								// if no dataset exist make a new one
								$objValue = new ContentValueModel();
							}
							
							$objValue->cid = $this->currentRecord;
							$objValue->pid = $this->pid;
							$objValue->rid = $this->rid;
							$objValue->tstamp = time();
							$objValue->count = $this->varValue + 1;
							
							$objValue->save();
							
							// (Re)Order values
							$this->insertMultiGroup($this->pid, $this->rid, \Input::get('cid'));
						}
					break;
					case 'up':
						// (Re)Order values
						$this->moveUpMultiGroup($this->pid, $this->rid, \Input::get('cid'));
					break;
					case 'down':
						// (Re)Order values
						$this->moveDownMultiGroup($this->pid, $this->rid, \Input::get('cid'));
					break;
					case 'delete':
						if ($this->varValue > 1)
						{
							$objValue = \ContentValueModel::findByCidandPidandRid($this->currentRecord, $this->pid, $this->rid);
							
							if ($objValue === null)
							{
								// if no dataset exist make a new one
								$objValue = new ContentValueModel();
							}
							
							$objValue->cid = $this->currentRecord;
							$objValue->pid = $this->pid;
							$objValue->rid = $this->rid;
							$objValue->tstamp = time();
							$objValue->count = $this->varValue - 1;
							
							$objValue->save();
							
							// (Re)Order values
							$this->deleteMultiGroup($this->pid, $this->rid, \Input::get('cid'));
						}
					break;
				}
				
				$this->redirect(preg_replace('/&(amp;)?cid=[^&]*/i', '', preg_replace('/&(amp;)?' . preg_quote($this->strCommand, '/') . '=[^&]*/i', '', \Environment::get('request'))));
			}

		}
		
		
		// Add hidden input fields		
		$return = '<input type="hidden" name="' . $this->strName . '" id="ctrl_' . $this->strId . '" value="' . $this->groupCount . '">';
		$return .= '<div class="tl_multigroup_header">';
		
		// Add new button
		if ($this->groupCount < $this->numberOfGroups)
		{
			$return .= '<a onclick="var form=document.getElementById(\'tl_content\');form.action=this.href;form.submit();return false;" href="'.$this->addToUrl('&amp;'.$this->strCommand.'=insert&amp;cid='.($this->rid*100-1).'&amp;id='.$this->currentRecord.'&amp;rt='.\RequestToken::get()).'" title="' . $GLOBALS['TL_LANG']['MSC']['mg_new']['top'] . '">' . \Image::getHtml('new.svg', 'new') . ' ' . $GLOBALS['TL_LANG']['MSC']['mg_new']['label'] . '</button>';
		}
		
		$return .= '</div>';

		return $return;
	}

	
	
	/**
	 * Insert pattern group and shift the pattern values recursively
	 *
	 * @param int $pid Pattern ID
	 * @param int $rid Multidimensional recursive ID
	 * @param int $cid Command ID (The recursive ID from wich the command was fired)
	 * @param int $shifter Internal decimal level
	 *
	 * @return string
	 */
	protected function insertMultiGroup($pid, $rid, $cid, $shifter=1)
	{
		// get sub pattern for pid
		$colPattern = \ContentPatternModel::findPublishedByPidAndTable($pid, 'tl_content_subpattern', array('order'=>'sorting ASC'));

		// Prepare the value array
		if ($colPattern !== null)
		{
			foreach($colPattern as $objPattern)
			{	
				// Load values from tl_content_value
				$colValues = \ContentValueModel::findByCidandPid($this->currentRecord, $objPattern->id);

				if ($colValues !== null)
				{
					foreach($colValues as $objValue)
					{
						if ($objValue->rid >= ($cid + 1) * $shifter && $objValue->rid < ($rid + $shifter) * 100)
						{
							$objValue->rid = $objValue->rid + $shifter;
							$objValue->save();
						}
					}
				}
					
				// Work recursive through sub sub pattern
				if (in_array($objPattern->type, $GLOBALS['TL_CTP_SUB']))
				{
					$this->insertMultiGroup($objPattern->id, $rid*100, $cid, $shifter*100);
				}	
			}
		}
	}

	/**
	 * Delete pattern group and shift the pattern values recursively
	 *
	 * @param int $pid Pattern ID
	 * @param int $rid Multidimensional recursive ID
	 * @param int $cid Command ID (The recursive ID from wich the command was fired)
	 * @param int $shifter Internal decimal level
	 *
	 * @return string
	 */
	protected function deleteMultiGroup($pid, $rid, $cid, $shifter=1)
	{
		// get sub pattern for pid
		$colPattern = \ContentPatternModel::findPublishedByPidAndTable($pid, 'tl_content_subpattern', array('order'=>'sorting ASC'));

		// Prepare the value array
		if ($colPattern !== null)
		{
			foreach($colPattern as $objPattern)
			{	
				// Load values from tl_content_value
				$colValues = \ContentValueModel::findByCidandPid($this->currentRecord, $objPattern->id);

				if ($colValues !== null)
				{
					foreach($colValues as $objValue)
					{
						if ($objValue->rid >= $cid * $shifter && $objValue->rid < ($cid + 1) * $shifter)
						{
							$objValue->delete();
						}
						else if ($objValue->rid >= ($cid + 1) * $shifter && $objValue->rid < ($rid + $shifter) * 100)
						{
							$objValue->rid = $objValue->rid - $shifter;
							$objValue->save();
						}
						
						
					}
					
					
				}
				
					
				// Work recursive through sub sub pattern
				if (in_array($objPattern->type, $GLOBALS['TL_CTP_SUB']))
				{
					$this->deleteMultiGroup($objPattern->id, $rid*100, $cid, $shifter*100);
				}	
			}
		}
	}

	
	/**
	 * Move a pattern group and shift the pattern values recursively
	 *
	 * @param int $pid Pattern ID
	 * @param int $rid Multidimensional recursive ID
	 * @param int $cid Command ID (The recursive ID from wich the command was fired)
	 * @param int $shifter Internal decimal level
	 *
	 * @return string
	 */
	protected function moveUpMultiGroup($pid, $rid, $cid, $shifter=1)
	{
		// get sub pattern for pid
		$colPattern = \ContentPatternModel::findPublishedByPidAndTable($pid, 'tl_content_subpattern', array('order'=>'sorting ASC'));

		// Prepare the value array
		if ($colPattern !== null)
		{
			foreach($colPattern as $objPattern)
			{	
				// Load values from tl_content_value
				$colValues = \ContentValueModel::findByCidandPid($this->currentRecord, $objPattern->id);

				if ($colValues !== null)
				{
					foreach($colValues as $objValue)
					{
						// shift lower rid one up
						if ($objValue->rid >= ($cid - 1) * $shifter && $objValue->rid < ($cid) * $shifter)
						{
							$objValue->rid = $objValue->rid + $shifter;
							$objValue->save();
						}
						// shift this rid one down
						else if ($objValue->rid >= ($cid) * $shifter && $objValue->rid < ($cid + 1) * $shifter)
						{
							$objValue->rid = $objValue->rid - $shifter;
							$objValue->save();
						}
					}
				}
					
				// Work recursive through sub sub pattern
				if (in_array($objPattern->type, $GLOBALS['TL_CTP_SUB']))
				{
					$this->moveUpMultiGroup($objPattern->id, $rid*100, $cid, $shifter*100);
				}	
			}
		}
	}

	
	/**
	 * Move a pattern group and shift the pattern values recursively
	 *
	 * @param int $pid Pattern ID
	 * @param int $rid Multidimensional recursive ID
	 * @param int $cid Command ID (The recursive ID from wich the command was fired)
	 * @param int $shifter Internal decimal level
	 *
	 * @return string
	 */
	protected function moveDownMultiGroup($pid, $rid, $cid, $shifter=1)
	{
		// get sub pattern for pid
		$colPattern = \ContentPatternModel::findPublishedByPidAndTable($pid, 'tl_content_subpattern', array('order'=>'sorting ASC'));

		// Prepare the value array
		if ($colPattern !== null)
		{
			foreach($colPattern as $objPattern)
			{	
				// Load values from tl_content_value
				$colValues = \ContentValueModel::findByCidandPid($this->currentRecord, $objPattern->id);

				if ($colValues !== null)
				{
					foreach($colValues as $objValue)
					{
						// shift this rid one down
						if ($objValue->rid >= ($cid) * $shifter && $objValue->rid < ($cid + 1) * $shifter)
						{
							$objValue->rid = $objValue->rid + $shifter;
							$objValue->save();
						}
						// shift lower rid one up
						else if ($objValue->rid >= ($cid + 1) * $shifter && $objValue->rid < ($cid + 2) * $shifter)
						{
							$objValue->rid = $objValue->rid - $shifter;
							$objValue->save();
						}
					}
				}
					
				// Work recursive through sub sub pattern
				if (in_array($objPattern->type, $GLOBALS['TL_CTP_SUB']))
				{
					$this->moveDownMultiGroup($objPattern->id, $rid*100, $cid, $shifter*100);
				}	
			}
		}
	}
	

}
