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
		$return = '<div class="tl_group_header"><div class="tl_content_right click2edit">';
		
		// Add new group button
		$return .= '<button onclick="AjaxRequest.insertGroup(this,\'' . $this->pattern . '\', 0, ' . $this->max . ')" type="button" class="insert-handle" title="' . $GLOBALS['TL_LANG']['MSC']['group']['new']['top'] . '"' . (($this->insert) ? '' : 'disabled') . '>' . \Image::getHtml('new.svg', 'new') . ' ' . $GLOBALS['TL_LANG']['MSC']['group']['new']['label'] . '</button>';
		
		$return .= '</div></div>';

		return $return;
	}
}