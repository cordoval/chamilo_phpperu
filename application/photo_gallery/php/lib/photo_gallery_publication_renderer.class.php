<?php
namespace application\photo_gallery;

use common\libraries\Utilities;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\ActionBarSearchForm;
use common\libraries\Path;
use common\libraries\ToolbarItem;
use common\libraries\Theme;

require_once Path :: get_common_libraries_class_path() . 'html/action_bar/action_bar_search_form.class.php';

abstract class PhotoGalleryPublicationRenderer
{
    const TYPE_TABLE = 'table';
    const TYPE_GALLERY = 'gallery_table';
    const TYPE_SLIDESHOW = 'slideshow';
    
    protected $browser;

    function __construct($browser)
    {
        $this->browser = $browser;
    }

    function get_browser()
    {
        return $this->browser;
    }

    static function factory($type, $browser)
    {
        $file = dirname(__FILE__) . '/renderer/' . $type . '_photo_gallery_publication_renderer.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('PhotoGalleryPublicationRendererTypeDoesNotExist', array('type' => $type)));
        }
        
        require_once $file;
        
        $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($type) . 'PhotoGalleryPublicationRenderer';
        return new $class($browser);
    }

    abstract function as_html();

    public function get_parameters($include_search = false)
    {
        $parameters = $this->get_browser()->get_parameters();
        //        $types = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE);
        //        if (is_array($types) && count($types))
        //        {
        //            $parameters[RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE] = $types;
        //        }
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->get_browser()->get_action_bar()->get_query();
        
        return $parameters;
    }

    public function get_condition()
    {
        return $this->get_browser()->get_condition();
    }

    function count_photo_gallery_publications($condition)
    {
        return $this->get_browser()->count_photo_gallery_publications($condition);
    }

    function retrieve_photo_gallery_publications($condition, $order_property, $offset, $count)
    {
        return $this->get_browser()->retrieve_photo_gallery_publications($condition, $order_property, $offset, $count);
    }

    function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        return $this->get_browser()->get_url($parameters, $filter, $encode_entities);
    }

    public function get_user()
    {
        return $this->get_browser()->get_user();
    }

    function get_publication_viewing_url($object)
    {
        return $this->get_browser()->get_publication_viewing_url($object);
    }

    function get_photo_gallery_actions(PhotoGallery $photo_gallery)
    {
        $actions = array();
        
        $viewing_url = $this->get_browser()->get_publication_viewing_url($photo_gallery);
        $actions[] = new ToolbarItem(Translation :: get('View', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_details.png', $viewing_url, ToolbarItem :: DISPLAY_ICON);
        
        if ($this->get_browser()->get_user()->is_platform_admin() || $photo_gallery->get_publisher() == $this->get_browser()->get_user_id())
        {
            $edit_url = $this->get_browser()->get_publication_editing_url($photo_gallery);
            $actions[] = new ToolbarItem(Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_edit.png', $edit_url, ToolbarItem :: DISPLAY_ICON);
            
            $delete_url = $this->get_browser()->get_publication_deleting_url($photo_gallery);
            $actions[] = new ToolbarItem(Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_delete.png', $delete_url, ToolbarItem :: DISPLAY_ICON, true);
        }
        
        return $actions;
    }

}
?>