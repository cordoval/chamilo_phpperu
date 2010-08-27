<?php
/**
 * $Id: alexia_manager.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.alexia
 */
require_once dirname(__FILE__) . '/component/alexia_publication_browser/alexia_publication_browser_table.class.php';
require_once dirname(__FILE__) . '/../alexia_data_manager.class.php';
require_once dirname(__FILE__) . '/../alexia_block.class.php';
/**
 * This application gives each user the possibility to maintain a personal
 * calendar.
 */
class AlexiaManager extends WebApplication
{
    const APPLICATION_NAME = 'alexia';

    const PARAM_DELETE_SELECTED = 'delete_selected';
    const PARAM_ALEXIA_ID = 'publication';

    const ACTION_BROWSE_PUBLICATIONS = 'browser';
    const ACTION_CREATE_PUBLICATION = 'publisher';
    const ACTION_VIEW_PUBLICATION = 'viewer';
    const ACTION_EDIT_PUBLICATION = 'editor';
    const ACTION_DELETE_PUBLICATION = 'deleter';
    const ACTION_PUBLISH_INTRODUCTION = 'introducer';
    const ACTION_EDIT_INTRODUCTION = 'reintroducer';

    const DEFAULT_ACTION = self :: ACTION_BROWSE_PUBLICATIONS;

    /**
     * Constructor
     * @param int $user_id
     */
    public function AlexiaManager($user)
    {
        parent :: __construct($user);

        $this->parse_input_from_table();
    }

    /**
     * Renders the Alexia block and returns it.
     */
    function render_block($block)
    {
        $alexia_block = AlexiaBlock :: factory($this, $block);
        return $alexia_block->run();
    }

    /**
     * Gets the url for viewing a profile publication
     * @param ProfilePublication
     * @return string The url
     */
    function get_publication_viewing_url($alexia_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_PUBLICATION, self :: PARAM_ALEXIA_ID => $alexia_publication->get_id()));
    }

    function get_publication_editing_url($alexia_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_PUBLICATION, self :: PARAM_ALEXIA_ID => $alexia_publication->get_id()));
    }

    function get_introduction_editing_url($introduction)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_INTRODUCTION, self :: PARAM_ALEXIA_ID => $introduction->get_id()));
    }

    function get_publication_deleting_url($alexia_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_PUBLICATION, self :: PARAM_ALEXIA_ID => $alexia_publication->get_id()));
    }

    function count_alexia_publications($condition = null)
    {
        $adm = AlexiaDataManager :: get_instance();
        return $adm->count_alexia_publications($condition);
    }

    function retrieve_alexia_publication($id)
    {
        $adm = AlexiaDataManager :: get_instance();
        return $adm->retrieve_alexia_publication($id);
    }

    function retrieve_alexia_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        $adm = AlexiaDataManager :: get_instance();
        return $adm->retrieve_alexia_publications($condition, $offset, $max_objects, $order_by);
    }

    /**
     * Parse the input from the sortable tables and process input accordingly
     */
    private function parse_input_from_table()
    {
        $action = Request :: post('action');

        if (isset($action))
        {
            $selected_ids = Request :: post(AlexiaPublicationBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX);
            if (empty($selected_ids))
            {
                $selected_ids = array();
            }
            elseif (! is_array($selected_ids))
            {
                $selected_ids = array($selected_ids);
            }
            switch ($action)
            {
                case self :: PARAM_DELETE_SELECTED :
                    $this->set_action(self :: ACTION_DELETE_PUBLICATION);
                    Request :: set_get(self :: PARAM_ALEXIA_ID, $selected_ids);
                    break;
            }
        }
    }

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: APPLICATION_NAME
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: APPLICATION_NAME in the context of this class
     * - YourApplicationManager :: APPLICATION_NAME in all other application classes
     */
    function get_application_name()
    {
        return self :: APPLICATION_NAME;
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