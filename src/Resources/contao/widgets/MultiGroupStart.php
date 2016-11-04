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
 * @param  array   $options
 * @param  boolean $multiple
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
		
		$return = '
		
		<div class="multigroup clr" data-rid="' . $this->rid . '" style="background: rgba(200,200,200,.15); margin: 0 -10px 8px; padding: 10px 10px 18px;">
<div class="multigroup_header clr" style="margin: 0 0 8px;">
<div class="multigroup_right" style="padding: 1px 0 1px; float: right; text-align: right;">';

		if ($this->up)
		{
			$return .= '<span class="up"><a href="'.$this->addToUrl('&amp;'.$this->strCommand.'=up&amp;cid='.$this->cid.'&amp;id='.$this->currentRecord.'&amp;rt='.\RequestToken::get()).'">Up</a></span>';

		}
		if ($this->down)
		{
			$return .= ' <span class="down"><a href="'.$this->addToUrl('&amp;'.$this->strCommand.'=down&amp;cid='.$this->cid.'&amp;id='.$this->currentRecord.'&amp;rt='.\RequestToken::get()).'">Down</a></span>';

		}
		if ($this->delete)
		{
			$return .= ' <span class="delete"><a href="'.$this->addToUrl('&amp;'.$this->strCommand.'=delete&amp;cid='.$this->cid.'&amp;id='.$this->currentRecord.'&amp;rt='.\RequestToken::get()).'">Delete</a></span>';

		}
		if ($this->insert)
		{
				$return .= ' <span class="insert"><a href="'.$this->addToUrl('&amp;'.$this->strCommand.'=insert&amp;cid='.$this->cid.'&amp;id='.$this->currentRecord.'&amp;rt='.\RequestToken::get()).'">Insert</a></span>';
		}
		
		$return .= '</div>
<h3 style="padding-top:1px"><label>
'.$this->title.'
</label></h3>
<p class="tl_help tl_tip" title="">'.$this->desc.'</p>
</div>	
		<div>';
					
		return $return;
	}


}
