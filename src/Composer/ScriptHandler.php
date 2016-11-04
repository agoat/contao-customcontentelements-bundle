<?php

/*
 * This file is part of Contao.
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Agoat\ContentBlocks\Composer;

use Composer\Script\Event;
use Contao\Script\Event;


/**
 * Sets up the Contao environment in a Symfony app.
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class ScriptHandler
{

    /**
     * Sets the environment variable for the random secret.
     *
     * @param Event $event
     */
    public static function updatePatternTable(Event $event)
    {
        $db = Database::getInstance();
		
		$db->prepare("UPDATE tl_content_pattern SET ptable='tl_content_blocks' WHERE ptable=''")->execute();
    }



}
