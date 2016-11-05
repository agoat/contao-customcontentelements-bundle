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
		
		$return = '<div class="tl_multigroup clr" data-rid="' . $this->rid . '">';
		$return .= '<div class="tl_multigroup_right click2edit">';

		if ($this->up)
		{
			$return .= '<a href="'.$this->addToUrl('&amp;'.$this->strCommand.'=up&amp;cid='.$this->cid.'&amp;id='.$this->currentRecord.'&amp;rt='.\RequestToken::get()).'">' . \Image::getHtml('up.svg', 'up', 'title="' . $GLOBALS['TL_LANG']['MSC']['mg_up'] . '"') . '</a>';

		}
		if ($this->down)
		{
			$return .= ' <a href="'.$this->addToUrl('&amp;'.$this->strCommand.'=down&amp;cid='.$this->cid.'&amp;id='.$this->currentRecord.'&amp;rt='.\RequestToken::get()).'">' . \Image::getHtml('down.svg', 'down', 'title="' . $GLOBALS['TL_LANG']['MSC']['mg_down'] . '"') . '</a>';

		}
		if ($this->delete)
		{
			$return .= ' <a href="'.$this->addToUrl('&amp;'.$this->strCommand.'=delete&amp;cid='.$this->cid.'&amp;id='.$this->currentRecord.'&amp;rt='.\RequestToken::get()).'">' . \Image::getHtml('delete.svg', 'delete', 'title="' . $GLOBALS['TL_LANG']['MSC']['mg_delete'] . '"') . '</a>';

		}
		if ($this->insert)
		{
			$return .= ' <a href="'.$this->addToUrl('&amp;'.$this->strCommand.'=insert&amp;cid='.$this->cid.'&amp;id='.$this->currentRecord.'&amp;rt='.\RequestToken::get()).'">' . \Image::getHtml('new.svg', 'new', 'title="' . $GLOBALS['TL_LANG']['MSC']['mg_new']['after'] . '"') . '</a>';
		}
		
		$return .= '</div>';
		$return .= '<h3><label>' . $this->title . '</label></h3>';
		$return .= '<p class="tl_help tl_tip" title="">' . $this->desc . '</p>';	
		$return .= '<div class="tl_multigroup_box">';
					
		return $return;
	}


}
