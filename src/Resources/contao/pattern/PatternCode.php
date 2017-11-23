<?php

/*
 * Custom content elements extension for Contao Open Source CMS.
 *
 * @copyright  Arne Stappen (alias aGoat) 2017
 * @package    contao-contentelements
 * @author     Arne Stappen <mehh@agoat.xyz>
 * @link       https://agoat.xyz
 * @license    LGPL-3.0
 */

namespace Agoat\ContentElements;

use Contao\StringUtil;


/**
 * Content element pattern "code"
 */
class PatternCode extends Pattern
{
	/**
	 * Creates the DCA configuration
	 */
	public function create()
	{
		// The highlight select field
		if ($this->canChangeHighlight)
		{
			$this->generateDCA('highlight', array
			(
				'inputType'			=> 'select',
				'label'				=> &$GLOBALS['TL_LANG']['tl_content']['highlight'],
				'options'			=> array('HTML', 'HTML5', 'XML', 'JavaScript', 'CSS', 'SCSS', 'PHP', 'JSON', 'Markdown'),
				'eval'				=> array
				(
					'includeBlankOption'	=> true,
					'submitOnChange'		=> true,
					'tl_class'				=> 'w50 clr'
				),
			));		
		}

		// The code text area
		$this->generateDCA('text', array
		(
			'inputType' 	=>	'textarea',
			'label'			=>	array($this->label, $this->description),
			'eval'			=>	array
			(
				'mandatory'		=>	($this->mandatory) ? true : false, 
				'tl_class'		=> 	'clr',
				'rte'			=>	'ace|' . (($this->highlight) ? strtolower($this->highlight) : 'text'),
				'preserveTags'	=>	true,
			),
			'load_callback'		=> (!$this->canChangeHighlight) ?: array
			(
				array('tl_content_elements', 'setAceCodeHighlighting'),
			),
		));
	}
	

	/**
	 * Generate the pattern preview
	 *
	 * @return string HTML code
	 */
	public function preview()
	{
		$selector = 'ctrl_textarea' . $this->id;

		if ($this->canChangeHighlight)
		{
			$strPreview = '<div class="widget" style="padding-top:10px;"><h3 style="margin: 0;"><label>' . $GLOBALS['TL_LANG']['tl_pattern']['highlight'][0] . '</label></h3>';
			$strPreview .= '<select class="tl_select" style="width: 412px;">';
			$strPreview .= '<option value="">-</option><option value="HTML">HTML</option><option value="HTML5">HTML5</option><option value="XML">XML</option><option value="JavaScript">JavaScript</option><option value="CSS">CSS</option><option value="SCSS">SCSS</option><option value="PHP">PHP</option><option value="JSON">JSON</option><option value="Markdown">Markdown</option>';
			$strPreview .= '</select><p title="" class="tl_help tl_tip">' . $GLOBALS['TL_LANG']['tl_pattern']['highlight'][1] . '</p></div>';
		}

		$strPreview .= '<div class="widget" style="padding-top:10px;"><h3 style="margin: 0;"><label>' . $this->label . '</label></h3>';
		$strPreview .= '<textarea id="' . $selector . '" aria-hidden="true" class="tl_textarea noresize" rows="12" cols="80"></textarea>';
		
		$objTemplate = new \BackendTemplate('be_ace');
		$objTemplate->selector = $selector;
		$objTemplate->type = strtolower($this->highlight);
		
		$strPreview .= $objTemplate->parse();
		
		$strPreview .= '<p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';	

		return $strPreview;
	}


	/**
	 * Prepare the data for the template
	 */
	public function compile()
	{
		$this->writeToTemplate(array('code' => ($this->htmlspecialchars) ? StringUtil::specialchars($this->data->text) : $this->data->text, 'highlight' => ($this->canChangeHighlight) ? $this->data->highlight : $this->highlight));
	}
}
