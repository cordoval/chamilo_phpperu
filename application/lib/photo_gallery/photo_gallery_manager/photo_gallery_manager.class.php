<?php
/**
 * $Id: photo_gallery_manager.class.php
 * @package application.photo_gallery
 */
require_once dirname(__FILE__) . '/../photo_gallery_data_manager.class.php';

class PhotoGalleryManager extends WebApplication
{
    const APPLICATION_NAME = 'photo_gallery';
    const PARAM_PHOTO_GALLERY_ID = 'photo_id';
    const PARAM_RENDERER = 'renderer';

    const ACTION_BROWSE = 'browser';
//    const ACTION_VIEW = 'viewer';

    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    /**
     * Constructor
     * @param int $user_id
     */
    public function PhotoGalleryManager($user)
    {
        parent :: __construct($user);

        $this->parse_input_from_table();
    }

    function count_photo_gallery($condition = null)
    {
        $adm = PhotoGalleryDataManager :: get_instance();
        return $adm->count_photo_gallery($condition);
    }

    function retrieve_photo_gallery($id)
    {
        $adm = PhotoGalleryDataManager :: get_instance();
        return $adm->retrieve_alexia_publication($id);
    }

    function retrieve_photos_gallery($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        $adm = AlexiaDataManager :: get_instance();
        return $adm->retrieve_photos_gallery($condition, $offset, $max_objects, $order_by);
    }

    /**
     * Parse the input from the sortable tables and process input accordingly
     */
    private function parse_input_from_table()
    {
        $action = Request :: post('action');

        if (isset($action))
        {
            $selected_ids = Request :: post(PhotoGalleryBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX);
            if (empty($selected_ids))
            {
                $selected_ids = array();
            }
            elseif (! is_array($selected_ids))
            {
                $selected_ids = array($selected_ids);
            }
        }
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
        return array(PhotoGalleryRenderer :: TYPE_TABLE, PhotoGalleryRenderer :: TYPE_GALLERY, PhotoGalleryRenderer :: TYPE_SLIDESHOW);
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