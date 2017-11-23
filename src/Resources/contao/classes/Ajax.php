<?php

/*
 * Custom content elements extension for Contao Open Source CMS.
 *
 * @copyright  Arne Stappen (alias aGoat) 2017
 * @package    contao-customcontentelements
 * @author     Arne Stappen <mehh@agoat.xyz>
 * @link       https://agoat.xyz
 * @license    LGPL-3.0
 */

namespace Agoat\CustomContentElementsBundle\Contao;
 
use Contao\CoreBundle\Exception\ResponseException;
use Symfony\Component\HttpFoundation\Response;


/**
 * Ajax class
 */
class Ajax extends \Backend
{
	/**
	 * Handle multi- and subpattern AJAX requests
	 *
	 * @param string        $strAction
	 * @param DataContainer $dc
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
