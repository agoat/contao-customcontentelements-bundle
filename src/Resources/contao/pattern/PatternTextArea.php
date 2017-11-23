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

use Contao\TemplateLoader;
use Symfony\Component\Filesystem\Filesystem;


/**
 * Content element pattern "textarea"
 */
class PatternTextArea extends Pattern
{
	/**
	 * Creates the DCA configuration
	 */
	public function create()
	{
		// Register all tinyMCE template files
		if (!array_key_exists($this->rteTemplate, TemplateLoader::getFiles()))
		{
			$objFilesystem = new Filesystem();

			$arrTemplateFiles = glob(TL_ROOT . '/templates/*/be_tinyMCE*');
			
			foreach ($arrTemplateFiles as $strFile)
			{
				$arrTemplates[basename($strFile, '.html5')] = rtrim($objFilesystem->makePathRelative(dirname($strFile), TL_ROOT), '/');
			}
			
			if ($arrTemplates !== null)
			{
				TemplateLoader::addFiles($arrTemplates);
			}
		}

		$this->generateDCA('text', array
		(
			'inputType' 	=>	'textarea',
			'label'			=>	array($this->label, $this->description),
			'eval'			=>	array
			(
				'mandatory'		=>	($this->mandatory) ? true : false, 
				'tl_class'		=> 	'clr',
				'rte'			=>	(array_key_exists($this->rteTemplate, TemplateLoader::getFiles())) ? substr($this->rteTemplate, 3) : 'tinyMCE',
				'preserveTags'	=>	true,
			)
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

		$strPreview = '<div class="widget" style="padding-top:10px;"><h3 style="margin: 0;"><label>' . $this->label . '</label></h3>';
		$strPreview .= '<textarea id="' . $selector . '" aria-hidden="true" class="tl_textarea noresize" rows="12" cols="80"></textarea>';
		
		// Register all tinyMCE template files
		if (!array_key_exists($this->rteTemplate, TemplateLoader::getFiles()))
		{
			$objFilesystem = new Filesystem();
			
			$arrTemplateFiles = glob(TL_ROOT . '/templates/*/be_tinyMCE*');

			foreach ($arrTemplateFiles as $strFile)
			{
				$arrTemplates[basename($strFile, '.html5')] = rtrim($objFilesystem->makePathRelative(dirname($strFile), TL_ROOT), '/');
			}
			
			if ($arrTemplates !== null)
			{
				TemplateLoader::addFiles($arrTemplates);
			}
		}

		if (array_key_exists($this->rteTemplate, TemplateLoader::getFiles()))
		{
			$objTemplate = new \BackendTemplate($this->rteTemplate);
			$objTemplate->selector = $selector;

			$strPreview .= $objTemplate->parse();
		}
			
		$strPreview .= '<p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';	

		return $strPreview;
	}


	/**
	 * Prepare the data for the template
	 */
	public function compile()
	{
		$this->writeToTemplate($this->data->text);
	}
}
