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
		if ($varInput > $this->maxGroups)
		{
			$varInput = $this->maxGroups;
		}
		else if ($varInput < 1)
		{
			$varInput = 1;
		}
		
		return parent::validator($varInput);
	}

	
	protected function insertMultiGroup($pid, $irid, $prid)
	{
		// get pattern for pid
		$colPattern = \ContentPatternModel::findPublishedByPidAndTable($pid, 'tl_content_subpattern', array('order'=>'sorting ASC'));
		
		$arrValues = array();
		
		// Prepare the value array
		if ($colPattern !== null)
		{
			foreach($colPattern as $objPattern)
			{	
				// Load values from tl_content_value
				$colValues = \ContentValueModel::findByCidandPid($this->currentRecord, $objPattern->id);

				$arrValues = ($colValues !== null) ? $colValues->fetchAll() : array();
				
				krsort($arrValues);
			dump($arrValues);	
				// Bring values in the right order (rid)
				if ($arrValues !== null)
				{
					foreach ($arrValues as $value)
					{
						if ($value['rid'] >= \Input::get('prid') && $value['rid'] < \Input::get('prid')+100)
						{
							$arrNewValues[$value['rid']][$value['pid']] = $value;
						}
						
					}							
				}
				
				// Load and append Pattern of sub multi groups
				if (in_array($objPattern->type, $GLOBALS['TL_CTP_SUB']))
				{
					// call this function again ..
				}
				
			}
		dump($arrNewValues);
		}
	}
	
	/**
	 * Generate the widget and return it as string
	 *
	 * @return string
	 */
	public function generate()
	{
		// a function to reorder (insert, move up/down, delete) the related values from tl_content_value
		// problem is the multidimensional rid value !!!!
		
		
		
		
		// Change content values order
		if (\Input::get($this->strCommand) && is_numeric(\Input::get('irid')) && is_numeric(\Input::get('prid')) && \Input::get('id') == $this->currentRecord)
		{

			// make value a number
			if (is_null($this->varValue))
			{
				$this->varValue = 1;
			}

			// Correct and save group counter
			switch(\Input::get($this->strCommand))
			{
				case 'insert':
					dump('insert');
					if ($this->varValue < $this->maxGroups)
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
						$objValue->groupCount = $this->varValue + 1;
						
						$objValue->save();
						
						// Order values
						$this->insertMultiGroup($this->pid, \Input::get('irid'), \Input::get('prid'));
						
					}
					break;
				case 'up':
					dump('up');
					break;
				case 'down':
					dump('down');
					break;
				case 'delete':
					dump('delete');
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
						$objValue->groupCount = $this->varValue - 1;
						
						$objValue->save();
						
					}
					// if rid >> shifter--
					break;
					
			}
	dump($newValues);		
		
			// Save ordered values


			$this->redirect(preg_replace('/&(amp;)?prid=[^&]*/i', '', preg_replace('/&(amp;)?irid=[^&]*/i', '', preg_replace('/&(amp;)?' . preg_quote($this->strCommand, '/') . '=[^&]*/i', '', \Environment::get('request')))));
			
		}
		
		
		// \input::get(strCommand)
		// \inpout::get(rid) == action id
		
		// get sub pattern for multi group
		// foreach sub pattern
		
		// get values by cid and pid
		// foreach value make array[rid]
		
		// when add
		// foreach rid in array (if cid >> insert blank)
		// numberofGroups++
		
		// when up
		// foreach rid in array (id cid-1 >> )

		// when down
		// array_move_down($array, \inpout::get(rid))

		// when delte
		// array_delete($array, \inpout::get(rid))
		// numberofGroups--
		
		// save values back
		// foreach value ...
		
		// redirect (remove strcommand)
		
		
		// Add hidden input fields		
		return sprintf('<input type="hidden" name="%s" id="ctrl_%s" value="' . $this->numberOfGroups . '">
				<div class="multigroup_header clr" style="margin: 18px 0 8px; text-align: right;">
		<span class="insert"><a href="'.$this->addToUrl('&amp;'.$this->strCommand.'=insert&amp;irid=0&amp;prid='.$this->rid.'&amp;id='.$this->currentRecord.'&amp;rt='.\RequestToken::get()).'">Insert</a></span>
		</div>

		',$this->strName,$this->strId);
		
	}


}
