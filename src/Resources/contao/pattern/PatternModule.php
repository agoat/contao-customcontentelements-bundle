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

namespace Agoat\ContentBlocks;

use Agoat\ContentBlocks\Pattern;


class PatternModule extends Pattern
{


	/**
	 * generate the DCA construct
	 */
	public function construct()
	{
		// nothing to select
		return;
	}
	

	/**
	 * Generate backend output
	 */
	public function view()
	{
		$objModule = \ModuleModel::findByPk($this->module);
		return '<span>' . $objModule->name . ' (ID ' . $objModule->id . ')</span>';
	}

	/**
	 * Generate data for the frontend template 
	 */
	public function compile()
	{
		$objModule = \ModuleModel::findByPk($this->module);
	
		if ($objModule === null)
		{
			return;
		}
		
		$strClass = \Module::findClass($objModule->type);
		
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
