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
class MultiGroupStart extends \Widget
{


	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_widget_rdo';



	/**
	 * Generate the widget and return it as string
	 *
	 * @return string
	 */
	public function generate()
	{
		$strCommand = 'cmd_multigroup-' . $this->groupId . '-' . $this->rid;
		
		return '
		
		<div class="multigroup clr" data-rid="' . $this->rid . '" style="background: rgba(100,100,100,.1); margin: 0 -10px 18px; padding: 10px 10px 18px;">
		<div class="multigroup_header clr" style="text-align: right;">
		<span class="up"><a href="'.$this->addToUrl('&amp;'.$this->strCommand.'=up&amp;irid='.$this->irid.'&amp;prid='.$this->prid.'&amp;id='.$this->currentRecord.'&amp;rt='.\RequestToken::get()).'">Up</a></span> |
		<span class="down"><a href="'.$this->addToUrl('&amp;'.$this->strCommand.'=down&amp;irid='.$this->irid.'&amp;prid='.$this->prid.'&amp;id='.$this->currentRecord.'&amp;rt='.\RequestToken::get()).'">Down</a></span> |
		<span class="delete"><a href="'.$this->addToUrl('&amp;'.$this->strCommand.'=delete&amp;irid='.$this->irid.'&amp;prid='.$this->prid.'&amp;id='.$this->currentRecord.'&amp;rt='.\RequestToken::get()).'">Delete</a></span>
		</div>
		<div>';
							
	}


}
