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

use Contao\TemplateLoader;


class PatternCode extends Pattern
{
	/**
	 * generate the DCA construct
	 */
	public function construct()
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
				'rte'			=>	'ace|' . strtolower($this->highlight),
				'preserveTags'	=>	true,
			)
		));
		
	}
	

	/**
	 * Generate backend output
	 */
	public function view()
	{
		if ($this->canChangeHighlight)
		{
			$strPreview = '<div class="" style="padding-top:10px;"><h3 style="margin: 0;"><label>' . $GLOBALS['TL_LANG']['tl_content_pattern']['highlight'][0] . '</label></h3>';
			$strPreview .= '<select class="tl_select" style="width: 412px;">';
			$strPreview .= '<option value="">-</option><option value="HTML">HTML</option><option value="HTML5">HTML5</option><option value="XML">XML</option><option value="JavaScript">JavaScript</option><option value="CSS">CSS</option><option value="SCSS">SCSS</option><option value="PHP">PHP</option><option value="JSON">JSON</option><option value="Markdown">Markdown</option>';
			$strPreview .= '</select><p title="" class="tl_help tl_tip">' . $GLOBALS['TL_LANG']['tl_content_pattern']['highlight'][1] . '</p></div>';
		}

		$strPreview .= '<div class="" style="padding-top:10px;"><h3 style="margin: 0;"><label>' . $this->label . '</label></h3>';
		$this->selector = $selector = 'ctrl_textarea' . $this->id;
		$type = strtolower($this->highlight);

		$strPreview .= '<textarea id="' . $selector . '" aria-hidden="true" class="tl_textarea noresize" rows="12" cols="80"></textarea>';
		
		ob_start();
		include(TemplateLoader::getPath('be_ace', 'html5'));
		$strPreview .= ob_get_contents();
		ob_end_clean();
			
		$strPreview .= '<p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';	

		return $strPreview;
	}


	/**
	 * prepare data for the frontend template 
	 */
	public function compile()
	{
		// prepare value(s)		
		$this->writeToTemplate(array('code' => $this->Value->text, 'highlight' => ($this->canChangeHighlight) ? $this->Value->highlight : $this->highlight));
	}
	
}
