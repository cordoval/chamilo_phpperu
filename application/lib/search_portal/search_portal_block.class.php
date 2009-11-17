<?php
/**
 * $Id: search_portal_block.class.php 222 2009-11-13 14:39:28Z chellee $
 * @package application.search_portal
 */

require_once Path :: get_library_path() . 'block.class.php';

/**
==============================================================================
 *	This class represents a general Search Portal Block.
 *
 *	@author Hans De bisschop
==============================================================================
 */

class SearchPortalBlock extends Block
{

    /**
     * Constructor.
     * @param array $types The learning object types that may be published.
     * @param  boolean $email_option If true the publisher has the option to
     * send the published learning object by email to the selecter target users.
     */
    function SearchPortalBlock($parent, $block_info)
    {
        parent :: __construct($parent, $block_info);
    }

    /**
     * Create a new profiler component
     * @param string $type The type of the component to create.
     * @param Weblcms $weblcms The weblcms in
     * which the created component will be used
     */
    static function factory($search_portal, $block)
    {
        $type = $block->get_component();
        $filename = dirname(__FILE__) . '/block/search_portal_' . $type . '.class.php';
        if (! file_exists($filename) || ! is_file($filename))
        {
            die('Failed to load "' . $type . '" block');
        }
        $class = 'SearchPortal' . Utilities :: underscores_to_camelcase($type);
        require_once $filename;
        return new $class($search_portal, $block);
    }
}
?>