<?php
 
 /**
 * Contao Open Source CMS - ContentBlocks extension
 *
 * Copyright (c) 2017 Arne Stappen (aGoat)
 *
 *
 * @package   contentblocks
 * @author    Arne Stappen <http://agoat.de>
 * @license	  LGPL-3.0+
 */

namespace Agoat\ContentElements;
 
use Contao\CoreBundle\Exception\ResponseException;
use Symfony\Component\HttpFoundation\Response;


class Ajax extends \Backend
{

	/**
	 * Handle multi- and subpattern AJAX requests
	 */
	public function executeGroupActions ($strAction, $dc)
	{
		switch ($strAction)
		{
			case 'toggleSubpattern':
				if (!\Input::post('load'))
				{
					throw new ResponseException(new Response());
				}
			case 'switchSubpattern':
			case 'insertGroup':
				throw new ResponseException($this->convertToResponse($dc->edit(false, 'sub_' . \Input::post('pattern'))));
				
			case 'deleteGroup':
			case 'moveGroup':
				throw new ResponseException(new Response());
		}
	}

	/**
	 * Convert a string to a response object
	 *
	 * @param string $str
	 *
	 * @return Response
	 */
	protected function convertToResponse($str)
	{
		return new Response(\Controller::replaceOldBePaths($str));
	}
	
}
