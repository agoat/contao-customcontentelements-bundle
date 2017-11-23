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
 * @param  array   $options
 * @param  boolean $multiple
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class GroupStart extends \Widget
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
		$return = '<div class="tl_multigroup" data-rid="' . $this->rid . '">';
		$return .= '<div class="tl_multigroup_right click2edit">';

		if ($this->up)
		{
			$return .= '<a onclick="var form=document.getElementById(\'tl_content\');form.action=this.href;form.submit();return false;" href="'.$this->addToUrl('&amp;'.$this->strCommand.'=up&amp;cid='.$this->rid.'&amp;id='.$this->currentRecord.'&amp;rt='.\RequestToken::get()).'">' . \Image::getHtml('up.svg', 'up', 'title="' . $GLOBALS['TL_LANG']['MSC']['mg_up'] . '"') . '</a>';

		}
		if ($this->down)
		{
			$return .= ' <a onclick="var form=document.getElementById(\'tl_content\');form.action=this.href;form.submit();return false;" href="'.$this->addToUrl('&amp;'.$this->strCommand.'=down&amp;cid='.$this->rid.'&amp;id='.$this->currentRecord.'&amp;rt='.\RequestToken::get()).'">' . \Image::getHtml('down.svg', 'down', 'title="' . $GLOBALS['TL_LANG']['MSC']['mg_down'] . '"') . '</a>';

		}
		if ($this->delete)
		{
			$return .= ' <a onclick="Backend.getScrollOffset();var form=document.getElementById(\'tl_content\');form.action=this.href;form.submit();return false;" href="' . $this->addToUrl('&amp;' . $this->command . '=delete&amp;gid=' . $this->gid . '&amp;rt=' . \RequestToken::get()) . '">' . \Image::getHtml('delete.svg', 'new', 'title="' . $GLOBALS['TL_LANG']['MSC']['group']['delete'] . '"') . '</a>';
		}
		if ($this->insert)
		{
			$return .= ' <a onclick="Backend.getScrollOffset();var form=document.getElementById(\'tl_content\');form.action=this.href;form.submit();return false;" href="' . $this->addToUrl('&amp;' . $this->command . '=insert&amp;gid=' . $this->gid . '&amp;rt=' . \RequestToken::get()) . '">' . \Image::getHtml('new.svg', 'new', 'title="' . $GLOBALS['TL_LANG']['MSC']['group']['new']['after'] . '"') . '</a>';
		}
		
		$return .= '</div>';
		$return .= '<h3><label>' . $this->title . '</label></h3>';
		$return .= '<p class="tl_help tl_tip" title="">' . $this->desc . '</p>';	
		$return .= '<div>';
					
		return $return;
	}
}