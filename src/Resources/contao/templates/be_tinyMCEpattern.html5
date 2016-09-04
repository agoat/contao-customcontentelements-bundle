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

// get pattern object
$id = explode('_', $this->field);
$objPattern = \ContentPatternModel::findOneById($id[1]);

// get theme object
if ($this instanceof Contao\PatternTextArea) 
{
	$objBlock = \ContentBlocksModel::findOneById($this->pid);
	$objTheme = \ThemeModel::findOneById($objBlock->pid);
}
else
{
	$objLayout = \LayoutModel::findOneById(\ContentBlocks::getLayoutId($this->activeRecord->ptable, $this->activeRecord->pid));
	$objTheme = \ThemeModel::findOneById($objLayout->pid);
}


if ($objPattern === null)
{
	include(\TemplateLoader::getPath('tinymce_simple', 'html5'));
}
else
{
	include(\TemplateLoader::getPath($objPattern->rteTemplate, 'html5', $objTheme->templates));
}

?>