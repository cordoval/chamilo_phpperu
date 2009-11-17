<?php
/**
 * $Id: repository_block.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */

require_once Path :: get_library_path() . 'block.class.php';

/**
==============================================================================
 *	This class represents a general Admin Block.
 *
 *	@author Hans De bisschop
==============================================================================
 */

class RepositoryBlock extends Block
{

    /**
     * Constructor.
     */
    function RepositoryBlock($parent, $block_info)
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
        $filename = dirname(__FILE__) . '/../block/repository_' . $type . '.class.php';
        if (! file_exists($filename) || ! is_file($filename))
        {
            die('Failed to load "' . $type . '" block');
        }
        $class = 'Repository' . Utilities :: underscores_to_camelcase($type);
        require_once $filename;
        return new $class($users, $block);
    }
}
?>