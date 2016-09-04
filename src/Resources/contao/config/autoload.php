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


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'Agoat\\ContentBlocks'			=> 'system/modules/contentblocks/classes/ContentBlocks.php',
	'Agoat\\Pattern'				=> 'system/modules/contentblocks/classes/Pattern.php',
	'Agoat\\ContentBlockTemplate'	=> 'system/modules/contentblocks/classes/ContentBlockTemplate.php',
	'Agoat\\Theme'					=> 'system/modules/contentblocks/classes/Theme.php', // overwrite Theme class for import and export

	// Elements
	'Agoat\\ContentBlockElement'	=> 'system/modules/contentblocks/elements/ContentBlockElement.php',

	// Models
	'Agoat\\ContentBlocksModel'	=> 'system/modules/contentblocks/models/ContentBlocksModel.php',
	'Agoat\\ContentPatternModel'	=> 'system/modules/contentblocks/models/ContentPatternModel.php',
	'Agoat\\ContentValueModel'		=> 'system/modules/contentblocks/models/ContentValueModel.php',

	// Pattern
	'Agoat\\PatternTextField'		=> 'system/modules/contentblocks/pattern/PatternTextField.php',
	'Agoat\\PatternTextArea'		=> 'system/modules/contentblocks/pattern/PatternTextArea.php',
	'Agoat\\PatternCode'			=> 'system/modules/contentblocks/pattern/PatternCode.php',
	'Agoat\\PatternSelectField'	=> 'system/modules/contentblocks/pattern/PatternSelectField.php',
	'Agoat\\PatternCheckBox'		=> 'system/modules/contentblocks/pattern/PatternCheckBox.php',
	'Agoat\\PatternListWizard'		=> 'system/modules/contentblocks/pattern/PatternListWizard.php',
	'Agoat\\PatternTableWizard'	=> 'system/modules/contentblocks/pattern/PatternTableWizard.php',
	'Agoat\\PatternFileTree'		=> 'system/modules/contentblocks/pattern/PatternFileTree.php',

	'Agoat\\PatternSection'		=> 'system/modules/contentblocks/pattern/PatternSection.php',
	'Agoat\\PatternExplanation'	=> 'system/modules/contentblocks/pattern/PatternExplanation.php',
	
	'Agoat\\PatternVisibility'		=> 'system/modules/contentblocks/pattern/PatternVisibility.php',
	'Agoat\\PatternProtection'		=> 'system/modules/contentblocks/pattern/PatternProtection.php',

	'Agoat\\PatternForm'		=> 'system/modules/contentblocks/pattern/PatternForm.php',
	'Agoat\\PatternComment'	=> 'system/modules/contentblocks/pattern/PatternComment.php',
	'Agoat\\PatternModule'		=> 'system/modules/contentblocks/pattern/PatternModule.php',

	// Widgets
	'Agoat\\FileTree'			=> 'system/modules/contentblocks/widgets/FileTree.php', // overwrite FileTree widget
	'Agoat\\Explanation'		=> 'system/modules/contentblocks/widgets/Explanation.php', // new explanation widget (text for backend)
	'Agoat\\VisualSelectMenu'	=> 'system/modules/contentblocks/widgets/VisualSelectMenu.php', // new select menu with images
	
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'cb_standard' => 'system/modules/contentblocks/templates',
	'cb_simple' => 'system/modules/contentblocks/templates',
	'cb_debug' => 'system/modules/contentblocks/templates',
	'tinymce_standard' => 'system/modules/contentblocks/templates',
	'tinymce_simple' => 'system/modules/contentblocks/templates',
	'be_tinyMCEpattern' => 'system/modules/contentblocks/templates',
	'be_tinyMCEexplanation' => 'system/modules/contentblocks/templates',
));

