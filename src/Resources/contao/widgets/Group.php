<?php

/*
 * Custom content elements extension for Contao Open Source CMS.
 *
 * @copyright  Arne Stappen (alias aGoat) 2017
 * @package    contao-customcontentelements
 * @author     Arne Stappen <mehh@agoat.xyz>
 * @link       https://agoat.xyz
 * @license    LGPL-3.0
 */

namespace Agoat\CustomContentElementsBundle\Contao;


/**
 * Provide methods to handle the input field "group"
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
