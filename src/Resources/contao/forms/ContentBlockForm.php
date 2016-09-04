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


namespace Contao;
  

/**
 * Pattern class
 *
 * @property integer $id
 * @property integer $pid


 *
 * @author Arne Stappen
 */
class ContentBlockForm extends \Form
{
	/**
	 * Don´t remove anything. 
	 *
	 * @return string
	 */
	public function generate()
	{
		return \Hybrid::generate();
	}


}
