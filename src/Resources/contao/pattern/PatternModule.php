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

use Contao\Module;


/**
 * Content element pattern "module"
 */
class PatternModule extends Pattern
{
	/**
	 * Creates the DCA configuration
	 */
	public function create()
	{
		return; // Nothing to set
	}
	

	/**
	 * Generate the pattern preview
	 *
	 * @return string HTML code
	 */
	public function preview()
	{
		$objModule = \ModuleModel::findByPk($this->module);
		
		return '<div class="widget"><span style="color:#b3b3b3 ">' . $objModule->name . ' (ID ' . $objModule->id . ')</span></div>';
	}


	/**
	 * Prepare the data for the template
	 */
	public function compile()
	{
		$objModule = \ModuleModel::findByPk($this->module);
	
		if ($objModule === null)
		{
			return;
		}
		
		$strClass = Module::findClass($objModule->type);
		
		if (!class_exists($strClass))
		{
			return;
		}
	
		$objModule->typePrefix = 'ce_';

		/** @var \Module $objModule */
		$objModule = new $strClass($objModule);
		
		$this->writeToTemplate($objModule->generate());
	}
}
