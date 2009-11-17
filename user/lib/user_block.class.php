<?php
/**
 * $Id: user_block.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @author Sven Vanpoucke
 * @package user.lib
 */

require_once Path :: get_library_path() . 'block.class.php';

/**
==============================================================================
 *	This class represents a general Weblcms Block.
 *
 *	@author Hans De bisschop
==============================================================================
 */

class UserBlock extends Block
{

    /**
     * Constructor.
     * @param array $types The learning object types that may be published.
     * @param  boolean $email_option If true the publisher has the option to
     * send the published learning object by email to the selecter target users.
     */
    function UserBlock($parent, $block_info)
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
        $filename = dirname(__FILE__) . '/../block/user_' . $type . '.class.php';
        if (! file_exists($filename) || ! is_file($filename))
        {
            die('Failed to load "' . $type . '" block');
        }
        $class = 'User' . Utilities :: underscores_to_camelcase($type);
        require_once $filename;
        return new $class($users, $block);
    }
}
?>