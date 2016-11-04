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
		if ($varInput > $this->maxCount)
		{
			$varInput = $this->maxCount;
		}
		else if ($varInput < 1)
		{
			$varInput = 1;
		}
		
		return parent::validator($varInput);
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
		
		
dump($colPattern);

dump(($cid - 1) * $shifter);
dump(($cid) * $shifter);
dump(($cid + 1) * $shifter);

dump($shifter);

		// Prepare the value array
		if ($colPattern !== null)
		{
			foreach($colPattern as $objPattern)
			{	
				// Load values from tl_content_value
				$colValues = \ContentValueModel::findByCidandPid($this->currentRecord, $objPattern->id);

				if ($colValues !== null)
				{
dump($colValues);
					foreach($colValues as $objValue)
					{
						// shift lower rid one up
						if ($objValue->rid >= ($cid - 1) * $shifter && $objValue->rid < ($cid) * $shifter)
						{
							dump('shift up for pid '.$objValue->pid.' the rid from '.$objValue->rid.' to '.($objValue->rid + $shifter));

							$objValue->rid = $objValue->rid + $shifter;
							$objValue->save();
						}
						// shift this rid one down
						else if ($objValue->rid >= ($cid) * $shifter && $objValue->rid < ($cid + 1) * $shifter)
						{
							dump('shift down for pid '.$objValue->pid.' the rid from '.$objValue->rid.' to '.($objValue->rid - $shifter));

							$objValue->rid = $objValue->rid - $shifter;
							$objValue->save();
						}
						
						
					}
					
					
				}
				
					
				// Work recursive through sub sub pattern
				if (in_array($objPattern->type, $GLOBALS['TL_CTP_SUB']))
				{
					dump('working throug sub sub pattern / pid'.$objPattern->id);
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
		
		
dump($colPattern);

//dump(($cid - 1) * $shifter);
dump(($cid) * $shifter);
dump(($cid + 1) * $shifter);
dump(($cid + 2) * $shifter);

dump($shifter);

		// Prepare the value array
		if ($colPattern !== null)
		{
			foreach($colPattern as $objPattern)
			{	
				// Load values from tl_content_value
				$colValues = \ContentValueModel::findByCidandPid($this->currentRecord, $objPattern->id);

				if ($colValues !== null)
				{
dump($colValues);
					foreach($colValues as $objValue)
					{
						// shift this rid one down
						if ($objValue->rid >= ($cid) * $shifter && $objValue->rid < ($cid + 1) * $shifter)
						{
							dump('shift up for pid '.$objValue->pid.' the rid from '.$objValue->rid.' to '.($objValue->rid + $shifter));

							$objValue->rid = $objValue->rid + $shifter;
							$objValue->save();
						}
						// shift lower rid one up
						else if ($objValue->rid >= ($cid + 1) * $shifter && $objValue->rid < ($cid + 2) * $shifter)
						{
							dump('shift down for pid '.$objValue->pid.' the rid from '.$objValue->rid.' to '.($objValue->rid - $shifter));

							$objValue->rid = $objValue->rid - $shifter;
							$objValue->save();
						}
						
						
					}
					
					
				}
				
					
				// Work recursive through sub sub pattern
				if (in_array($objPattern->type, $GLOBALS['TL_CTP_SUB']))
				{
					dump('working throug sub sub pattern / pid'.$objPattern->id);
					$this->moveDownMultiGroup($objPattern->id, $rid*100, $cid, $shifter*100);
				}	
			}
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
		if (\Input::get($this->strCommand) && is_numeric(\Input::get('cid')) && \Input::get('id') == $this->currentRecord)
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
					if ($this->varValue < $this->maxCount)
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
						
						
						
						// (Re)Order values
						$this->insertMultiGroup($this->pid, $this->rid, \Input::get('cid'));
						
					}
					break;
				case 'up':
					dump('up');
					// (Re)Order values
					$this->moveUpMultiGroup($this->pid, $this->rid, \Input::get('cid'));
					break;
				case 'down':
					dump('down');
					// (Re)Order values
					$this->moveDownMultiGroup($this->pid, $this->rid, \Input::get('cid'));
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
						
						// (Re)Order values
						$this->deleteMultiGroup($this->pid, $this->rid, \Input::get('cid'));
					}

					break;
					
			}
			
		
			// Save ordered values


			$this->redirect(preg_replace('/&(amp;)?cid=[^&]*/i', '', preg_replace('/&(amp;)?' . preg_quote($this->strCommand, '/') . '=[^&]*/i', '', \Environment::get('request'))));
			
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
		$return = '<input type="hidden" name="' . $this->strName . '" id="ctrl_' . $this->strId . '" value="' . $this->numberOfGroups . '">
<div class="multigroup_header clr" style="margin: 0 0 8px;">';

		if ($this->numberOfGroups < $this->maxCount)
		{
			$return .= '<div class="insert" style="margin: 14px 0 1px; float: right;"><a href="'.$this->addToUrl('&amp;'.$this->strCommand.'=insert&amp;cid='.($this->rid*100-1).'&amp;&amp;id='.$this->currentRecord.'&amp;rt='.\RequestToken::get()).'">Insert</a></div>';
		}
		
		
		$return .= '</div>';


		return $return;
		
	}


}
