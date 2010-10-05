<?php
/**
 * $Id: photo_gallery_manager.class.php
 * @package application.photo_gallery
 */
require_once dirname(__FILE__) . '/../photo_gallery_data_manager.class.php';
require_once dirname(__FILE__) . '/../photo_gallery_publication_renderer.class.php';

class PhotoGalleryManager extends WebApplication
{
    const APPLICATION_NAME = 'photo_gallery';
    const PARAM_PHOTO_GALLERY_ID = 'photo_id';
    const PARAM_RENDERER = 'renderer';
    const PARAM_PUBLICATION = 'publication';
    
    const ACTION_BROWSE = 'browser';
    const ACTION_VIEW = 'viewer';
    const ACTION_PUBLISH = 'publisher';
    const ACTION_DELETE = 'deleter';
    const ACTION_EDIT = 'editor';
//    const ACTION_EDIT_PUBLICATION = 'edit_publication';
//    const ACTION_BROWSE_PUBLICATIONS = 'browse_publications';
//    const ACTION_DELETE_PUBLICATION = 'delete_publucation';
    
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    /**
     * Constructor
     * @param int $user_id
     */
    public function PhotoGalleryManager($user)
    {
    	parent :: __construct($user);
        $this->set_parameter(self :: PARAM_RENDERER, $this->get_renderer());
        spl_autoload_register('PhotoGalleryManager::__autoload');
    
    }

    static function __autoload($classname)
    {
        $list = array(
                'photo_gallery_publication' => 'photo_gallery_publication.class.php', 'photo_gallery_publication_user' => 'photo_gallery_publication_user.class.php', 
                'photo_gallery_publication_renderer' => 'photo_gallery_publication_renderer.class.php', 'photo_gallery_publication_group' => 'photo_gallery_publication_group.class.php', 
                'photo_gallery_data_manager' => 'photo_gallery_data_manager.class.php', 'photo_gallery_data_manager_interface' => 'photo_gallery_data_manager_interface.class.php', 
                'photo_gallery_publication_browser_table' => '/photo_gallery_publication_browser/photo_gallery_publication_browser_table.class.php', 
                'photo_gallery_publication_browser_table_column_model' => 'photo_gallery_publication_browser_table_column_model.class.php', 
                'default_photo_gallery_table_cell_renderer' => '/tables/photo_gallery_table/default_photo_gallery_table_cell_renderer.class.php', 'photo_gallery_manager' => '/photo_gallery_manager/photo_gallery_manager.class.php', 
                'default_photo_gallery_table_column_model' => '/tables/photo_gallery_table/default_photo_gallery_table_column_model.class.php');
        
        $lower_case = Utilities :: camelcase_to_underscores($classname);
        
        if (key_exists($lower_case, $list))
        {
            $url = $list[$lower_case];
            require_once dirname(__FILE__) . '/../' . $url;
            return true;
        }
        return false;
    }

    function count_photo_gallery_publications($condition = null)
    {
        $adm = PhotoGalleryDataManager :: get_instance();
        return $adm->count_photo_gallery_publications($condition);
    }

    function retrieve_photo_gallery_publication($id)
    {
        $adm = PhotoGalleryDataManager :: get_instance();
        return $adm->retrieve_photo_gallery_publication($id);
    }

    function retrieve_photo_gallery_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        $adm = PhotoGalleryDataManager :: get_instance();
        return $adm->retrieve_photo_gallery_publications($condition, $offset, $max_objects, $order_by);
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

    function get_publication_viewing_url($photo_gallery_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW, self :: PARAM_PHOTO_GALLERY_ID => $photo_gallery_publication->get_id()));
    }

    function get_publication_editing_url($photo_gallery_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT, self :: PARAM_PHOTO_GALLERY_ID => $photo_gallery_publication->get_id()));
    }

    function get_introduction_editing_url($introduction)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT, self :: PARAM_PHOTO_GALLERY_ID => $introduction->get_id()));
    }

    function get_publication_deleting_url($photo_gallery_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE, self :: PARAM_PHOTO_GALLERY_ID => $photo_gallery_publication->get_id()));
    }

    function get_available_renderers()
    {
        return array(PhotoGalleryPublicationRenderer :: TYPE_GALLERY, PhotoGalleryPublicationRenderer :: TYPE_SLIDESHOW, PhotoGalleryPublicationRenderer :: TYPE_TABLE);
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