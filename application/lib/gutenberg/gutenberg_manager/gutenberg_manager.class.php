<?php
/**
 * $Id: gutenberg_manager.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.gutenberg
 */
require_once dirname(__FILE__) . '/../gutenberg_data_manager.class.php';
require_once dirname(__FILE__) . '/../gutenberg_publication.class.php';
/**
 * This application gives each user the possibility to maintain a personal
 * calendar.
 */
class GutenbergManager extends WebApplication
{
    const APPLICATION_NAME = 'gutenberg';

    const PARAM_DELETE_SELECTED = 'delete_selected';
    const PARAM_GUTENBERG_ID = 'publication';
    const PARAM_RENDERER = 'renderer';

    const ACTION_BROWSE_PUBLICATIONS = 'browser';
    const ACTION_CREATE_PUBLICATION = 'publisher';
    const ACTION_VIEW_PUBLICATION = 'viewer';
    const ACTION_EDIT_PUBLICATION = 'editor';
    const ACTION_DELETE_PUBLICATION = 'deleter';

    const DEFAULT_ACTION = self :: ACTION_BROWSE_PUBLICATIONS;

    /**
     * Constructor
     * @param int $user_id
     */
    public function GutenbergManager($user)
    {
        parent :: __construct($user);
        $this->set_parameter(self :: PARAM_RENDERER, $this->get_renderer());
    }

    /**
     * Gets the url for viewing a profile publication
     * @param ProfilePublication
     * @return string The url
     */
    function get_publication_viewing_url($gutenberg_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_PUBLICATION, self :: PARAM_GUTENBERG_ID => $gutenberg_publication->get_id()));
    }

    function get_publication_editing_url($gutenberg_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_PUBLICATION, self :: PARAM_GUTENBERG_ID => $gutenberg_publication->get_id()));
    }

    function get_introduction_editing_url($introduction)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_INTRODUCTION, self :: PARAM_GUTENBERG_ID => $introduction->get_id()));
    }

    function get_publication_deleting_url($gutenberg_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_PUBLICATION, self :: PARAM_GUTENBERG_ID => $gutenberg_publication->get_id()));
    }

    function count_gutenberg_publications($condition = null)
    {
        $adm = GutenbergDataManager :: get_instance();
        return $adm->count_gutenberg_publications($condition);
    }

    function retrieve_gutenberg_publication($id)
    {
        $adm = GutenbergDataManager :: get_instance();
        return $adm->retrieve_gutenberg_publication($id);
    }

    function retrieve_gutenberg_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        $adm = GutenbergDataManager :: get_instance();
        return $adm->retrieve_gutenberg_publications($condition, $offset, $max_objects, $order_by);
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

    function get_renderer()
    {
        $renderer = Request :: get(self :: PARAM_RENDERER);

        if ($renderer && in_array($renderer, $this->get_available_renderers()))
        {
            return $renderer;
        }
        else
        {
            $renderers = $this->get_available_renderers();
            return $renderers[0];
        }
    }

    function get_available_renderers()
    {
        return array(GutenbergPublicationRenderer :: TYPE_TABLE, GutenbergPublicationRenderer :: TYPE_GALLERY, GutenbergPublicationRenderer :: TYPE_SLIDESHOW);
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