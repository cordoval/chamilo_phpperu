<?php
/**
 * $Id: admin_block.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib
 */


/**
==============================================================================
 *	This class represents a general Admin Block.
 *
 *	@author Hans De bisschop
==============================================================================
 */

class AdminBlock extends Block
{

    /**
     * Constructor.
     */
    function AdminBlock($parent, $block_info)
    {
        parent :: __construct($parent, $block_info);
    }

    /**
     * Create a new weblcms component
     * @param string $type The type of the component to create.
     * @param Weblcms $weblcms The weblcms in
     * which the created component will be used
     */
    static function factory($users, $block)
    {
        $type = $block->get_component();
        $filename = dirname(__FILE__) . '/../block/admin_' . $type . '.class.php';
        if (! file_exists($filename) || ! is_file($filename))
        {
            die('Failed to load "' . $type . '" block');
        }
        $class = 'Admin' . Utilities :: underscores_to_camelcase($type);
        require_once $filename;
        return new $class($users, $block);
    }
}
?>