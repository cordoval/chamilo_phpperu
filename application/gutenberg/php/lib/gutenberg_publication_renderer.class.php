<?php
require_once Path :: get_common_libraries_path(). 'html/action_bar/action_bar_search_form.class.php';

abstract class GutenbergPublicationRenderer
{
    const TYPE_TABLE = 'table';
    const TYPE_GALLERY = 'gallery_table';
    const TYPE_SLIDESHOW = 'slideshow';
    
    protected $browser;

    function GutenbergPublicationRenderer($browser)
    {
        $this->browser = $browser;
    }

    function get_browser()
    {
        return $this->browser;
    }

    static function factory($type, $browser)
    {
        $file = dirname(__FILE__) . '/renderer/' . $type . '_gutenberg_publication_renderer.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('GutenbergPublicationRendererTypeDoesNotExist', array('type' => $type)));
        }
        
        require_once $file;
        
        $class = Utilities :: underscores_to_camelcase($type) . 'GutenbergPublicationRenderer';
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

    function count_gutenberg_publications($condition)
    {
        return $this->get_browser()->count_gutenberg_publications($condition);
    }

    function retrieve_gutenberg_publications($condition, $order_property, $offset, $count)
    {
        return $this->get_browser()->retrieve_gutenberg_publications($condition, $order_property, $offset, $count);
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

    function get_gutenberg_publication_actions(GutenbergPublication $gutenberg_publication)
    {
        $actions = array();
        
        $viewing_url = $this->get_browser()->get_publication_viewing_url($gutenberg_publication);
        $actions[] = new ToolbarItem(Translation :: get('View'), Theme :: get_common_image_path() . 'action_details.png', $viewing_url, ToolbarItem :: DISPLAY_ICON);
        
        if ($this->get_browser()->get_user()->is_platform_admin() || $gutenberg_publication->get_publisher() == $this->get_browser()->get_user_id())
        {
            $edit_url = $this->get_browser()->get_publication_editing_url($gutenberg_publication);
            $actions[] = new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $edit_url, ToolbarItem :: DISPLAY_ICON);
            
            $delete_url = $this->get_browser()->get_publication_deleting_url($gutenberg_publication);
            $actions[] = new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $delete_url, ToolbarItem :: DISPLAY_ICON, true);
        }
        
        return $actions;
    }

}
?>