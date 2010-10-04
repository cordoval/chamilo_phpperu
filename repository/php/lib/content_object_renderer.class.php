<?php
require_once Path :: get_library_path() . 'html/action_bar/action_bar_search_form.class.php';

abstract class ContentObjectRenderer
{
    const TYPE_TABLE = 'table';
    const TYPE_GALLERY = 'gallery_table';
    const TYPE_SLIDESHOW = 'slideshow';
    
    protected $repository_browser;

    function ContentObjectRenderer($repository_browser)
    {
        $this->repository_browser = $repository_browser;
    }

    function get_repository_browser()
    {
        return $this->repository_browser;
    }

    static function factory($type, $external_repository_browser)
    {
        $file = dirname(__FILE__) . '/renderer/' . $type . '_content_object_renderer.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('ContentObjectRendererTypeDoesNotExist', array('type' => $type)));
        }
        
        require_once $file;
        
        $class = Utilities :: underscores_to_camelcase($type) . 'ContentObjectRenderer';
        return new $class($external_repository_browser);
    }

    abstract function as_html();

    public function get_parameters($include_search = false)
    {
        $parameters = $this->get_repository_browser()->get_parameters();
        $types = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE);
        if (is_array($types) && count($types))
        {
            $parameters[RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE] = $types;
        }
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->get_repository_browser()->get_action_bar()->get_query();
        
        return $parameters;
    }

    public function get_condition()
    {
        return $this->get_repository_browser()->get_condition();
    }

    function count_content_objects($condition)
    {
        return $this->get_repository_browser()->count_content_objects($condition);
    }

    function count_categories($conditions = null)
    {
        return $this->get_repository_browser()->count_categories($conditions);
    }

    function retrieve_content_objects($condition, $order_property, $offset, $count)
    {
        return $this->get_repository_browser()->retrieve_content_objects($condition, $order_property, $offset, $count);
    }

    function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        return $this->get_repository_browser()->get_url($parameters, $filter, $encode_entities);
    }

    function get_content_object_viewing_url($object)
    {
        return $this->get_repository_browser()->get_content_object_viewing_url($object);
    }

    function get_type_filter_url($type)
    {
        return $this->get_repository_browser()->get_type_filter_url($type);
    }

    function get_content_object_actions(ContentObject $content_object)
    {
        $actions = array();
        $actions[] = new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_repository_browser()->get_content_object_editing_url($content_object), ToolbarItem :: DISPLAY_ICON);
        
        if ($url = $this->get_repository_browser()->get_content_object_recycling_url($content_object))
        {
            $actions[] = new ToolbarItem(Translation :: get('Remove'), Theme :: get_common_image_path() . 'action_recycle_bin.png', $url, ToolbarItem :: DISPLAY_ICON, true);
        }
        else
        {
            $actions[] = new ToolbarItem(Translation :: get('RemoveNA'), Theme :: get_common_image_path() . 'action_recycle_bin_na.png', null, ToolbarItem :: DISPLAY_ICON);
        }
        
        if ($this->get_repository_browser()->count_categories(new EqualityCondition(RepositoryCategory :: PROPERTY_USER_ID, $this->get_repository_browser()->get_user_id())) > 0)
        {
            $actions[] = new ToolbarItem(Translation :: get('Move'), Theme :: get_common_image_path() . 'action_move.png', $this->get_repository_browser()->get_content_object_moving_url($content_object), ToolbarItem :: DISPLAY_ICON);
        }
        
        //$actions[] = new ToolbarItem(Translation :: get('Move'), Theme :: get_common_image_path() . 'action_move.png', $this->get_repository_browser()->get_content_object_moving_url($content_object), ToolbarItem :: DISPLAY_ICON);
        $actions[] = new ToolbarItem(Translation :: get('Metadata'), Theme :: get_common_image_path() . 'action_metadata.png', $this->get_repository_browser()->get_content_object_metadata_editing_url($content_object), ToolbarItem :: DISPLAY_ICON);
        $actions[] = new ToolbarItem(Translation :: get('Rights'), Theme :: get_common_image_path() . 'action_rights.png', $this->get_repository_browser()->get_content_object_rights_editing_url($content_object), ToolbarItem :: DISPLAY_ICON);
        $actions[] = new ToolbarItem(Translation :: get('Export'), Theme :: get_common_image_path() . 'action_export.png', $this->get_repository_browser()->get_content_object_exporting_url($content_object), ToolbarItem :: DISPLAY_ICON);
        $actions[] = new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_repository_browser()->get_publish_content_object_url($content_object), ToolbarItem :: DISPLAY_ICON);
        
        if ($this->get_repository_browser()->get_user()->is_platform_admin())
        {
            $actions[] = new ToolbarItem(Translation :: get('CopyToTemplates'), Theme :: get_common_image_path() . 'export_template.png', $this->get_repository_browser()->get_copy_content_object_url($content_object->get_id(), 0), ToolbarItem :: DISPLAY_ICON);
        }
        
        if ($content_object instanceof ComplexContentObjectSupport)
        {
            $actions[] = new ToolbarItem(Translation :: get('BrowseComplex'), Theme :: get_common_image_path() . 'action_build.png', $this->get_repository_browser()->get_browse_complex_content_object_url($content_object), ToolbarItem :: DISPLAY_ICON);
        }
        
        if ($content_object->get_type() == Document :: get_type_name())
        {
            $actions[] = new ToolbarItem(Translation :: get('Download'), Theme :: get_common_image_path() . 'action_download.png', $this->get_repository_browser()->get_document_downloader_url($content_object->get_id()), ToolbarItem :: DISPLAY_ICON);
        }
        
        return $actions;
    }

}
?>