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
use Symfony\Component\Filesystem\Filesystem;


class PatternTextArea extends Pattern
{


	/**
	 * generate the DCA construct
	 */
	public function construct()
	{
		// register all tinyMCE template files
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
	 * Generate backend output
	 */
	public function view()
	{
		$strPreview = '<div class="" style="padding-top:10px;"><h3 style="margin: 0;"><label>' . $this->label . '</label></h3>';

		$this->selector = 'ctrl_textarea' . $this->id;
		$this->field = 'pre_' . $this->id;
		
		$strPreview .= '<textarea id="' . $this->selector . '" aria-hidden="true" class="tl_textarea noresize" rows="12" cols="80"></textarea>';
		
		// register all tinyMCE template files
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
			ob_start();
			include(TemplateLoader::getPath($this->rteTemplate, 'html5'));
			$strPreview .= ob_get_contents();
			ob_end_clean();
		}
			
		$strPreview .= '<p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';	

		return $strPreview;
	}


	/**
	 * prepare data for the frontend template 
	 */
	public function compile()
	{
		// prepare value(s)
		
		$this->writeToTemplate($this->data->text);
	}
	
}
