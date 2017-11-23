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

namespace Agoat\ContentElements;


/**
 * Provide methods to show an explanation 
 *
 * @property array   $options
 * @property boolean $multiple
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class Group extends \Widget
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
		$return = '<div class="tl_multigroup_header">';
		
		// Add new group button
		if ($this->insert)
		{
			$return .= '<a onclick="Backend.getScrollOffset();var form=document.getElementById(\'tl_content\');form.action=this.href;form.submit();return false;" href="' . $this->addToUrl('&amp;' . $this->command . '=insert&amp;rt=' . \RequestToken::get()) . '" title="' . $GLOBALS['TL_LANG']['MSC']['group']['new']['top'] . '">' . \Image::getHtml('new.svg', 'new') . ' ' . $GLOBALS['TL_LANG']['MSC']['group']['new']['label'] . '</a>';
		}
		
		$return .= '</div>';

		return $return;
	}
}