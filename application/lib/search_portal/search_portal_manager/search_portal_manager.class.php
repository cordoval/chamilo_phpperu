<?php
/**
 * $Id: search_portal_manager.class.php 222 2009-11-13 14:39:28Z chellee $
 * @package application.search_portal.search_portal_manager
 */
require_once dirname(__FILE__) . '/../search_portal_block.class.php';

class SearchPortalManager extends WebApplication
{
    const APPLICATION_NAME = 'search_portal';

    const PARAM_USER = 'user';

    const ACTION_SEARCH = 'searcher';
    const ACTION_EMAIL_USER = 'user_emailer';

    const DEFAULT_ACTION = self :: ACTION_SEARCH;

    function SearchPortalManager($user = null)
    {
        parent :: __construct($user);
    }

    function get_email_user_url($user_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EMAIL_USER, self :: PARAM_USER => $user_id));
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    /**
     * Renders the search portal block and returns it.
     */
    function render_block($block)
    {
        $search_portal_block = SearchPortalBlock :: factory($this, $block);
        return $search_portal_block->run();
    }

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourApplicationManager :: DEFAULT_ACTION in all other application classes
     */
    function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

}
?>