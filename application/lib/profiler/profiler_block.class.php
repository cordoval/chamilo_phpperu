<?php
/**
 * $Id: profiler_block.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler
 */

require_once Path :: get_library_path() . 'block.class.php';

/**
==============================================================================
 *	This class represents a general Weblcms Block.
 *
 *	@author Hans De bisschop
==============================================================================
 */

class ProfilerBlock extends Block
{

    /**
     * Constructor.
     * @param array $types The learning object types that may be published.
     * @param  boolean $email_option If true the publisher has the option to
     * send the published learning object by email to the selecter target users.
     */
    function ProfilerBlock($parent, $block_info)
    {
        parent :: __construct($parent, $block_info);
    }

    /**
     * Create a new profiler component
     * @param string $type The type of the component to create.
     * @param Weblcms $weblcms The weblcms in
     * which the created component will be used
     */
    static function factory($profiler, $block)
    {
        $type = $block->get_component();
        $filename = dirname(__FILE__) . '/block/profiler_' . $type . '.class.php';
        if (! file_exists($filename) || ! is_file($filename))
        {
            die('Failed to load "' . $type . '" block');
        }
        $class = 'Profiler' . ucfirst($type);
        require_once $filename;
        return new $class($profiler, $block);
    }
}
?>