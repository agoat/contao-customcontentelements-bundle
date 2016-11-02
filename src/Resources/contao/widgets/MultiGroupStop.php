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
class MultiGroupStop extends \Widget
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
		
		return '</div></div>
				<div class="multigroup_header clr" style="margin: -8px 0 8px; text-align: right;">
		<span class="insert"><a href="'.$this->addToUrl('&amp;'.$this->strCommand.'=insert&amp;irid='.$this->irid.'&amp;prid='.$this->prid.'&amp;id='.$this->currentRecord.'&amp;rt='.\RequestToken::get()).'">Insert</a></span>
		</div>
';
							
	}


}
