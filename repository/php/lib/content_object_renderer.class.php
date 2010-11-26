<?php
namespace repository;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\ActionBarSearchForm;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\ComplexContentObjectSupport;

use repository\content_object\document\Document;

require_once Path :: get_library_path() . 'html/action_bar/action_bar_search_form.class.php';

abstract class ContentObjectRenderer
{
    const TYPE_TABLE = 'table';
    const TYPE_GALLERY = 'gallery_table';
    const TYPE_SLIDESHOW = 'slideshow';

    protected $repository_browser;

    function __construct($repository_browser)
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
            throw new Exception(Translation :: get('ContentObjectRendererTypeDoesNotExist', array(
                    'type' => $type)));
        }

        require_once $file;

        $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($type) . 'ContentObjectRenderer';
        return new $class($external_repository_browser);
    }

    abstract function as_html();

    public function get_parameters($include_search = false)
    {
        $parameters = $this->get_repository_browser()->get_parameters();
        $types = Request :: get(ContentObjectTypeSelector :: PARAM_CONTENT_OBJECT_TYPE);
        if (is_array($types) && count($types))
        {
            $parameters[ContentObjectTypeSelector :: PARAM_CONTENT_OBJECT_TYPE] = $types;
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
        $actions[] = new ToolbarItem(Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_edit.png', $this->get_repository_browser()->get_content_object_editing_url($content_object), ToolbarItem :: DISPLAY_ICON);

        if ($url = $this->get_repository_browser()->get_content_object_recycling_url($content_object))
        {
            $actions[] = new ToolbarItem(Translation :: get('Remove', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_recycle_bin.png', $url, ToolbarItem :: DISPLAY_ICON, true);
        }
        else
        {
            $actions[] = new ToolbarItem(Translation :: get('RemoveNotAvailable', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_recycle_bin_na.png', null, ToolbarItem :: DISPLAY_ICON);
        }

        if ($this->get_repository_browser()->count_categories(new EqualityCondition(RepositoryCategory :: PROPERTY_USER_ID, $this->get_repository_browser()->get_user_id())) > 0)
        {
            $actions[] = new ToolbarItem(Translation :: get('Move', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_move.png', $this->get_repository_browser()->get_content_object_moving_url($content_object), ToolbarItem :: DISPLAY_ICON);
        }

        //$actions[] = new ToolbarItem(Translation :: get('Metadata', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_metadata.png', $this->get_repository_browser()->get_content_object_metadata_editing_url($content_object), ToolbarItem :: DISPLAY_ICON);
        $actions[] = new ToolbarItem(Translation :: get('Share', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_rights.png', $this->get_repository_browser()->get_content_object_share_create_url($content_object->get_id()), ToolbarItem :: DISPLAY_ICON);
        $actions[] = new ToolbarItem(Translation :: get('Export', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_export.png', $this->get_repository_browser()->get_content_object_exporting_url($content_object), ToolbarItem :: DISPLAY_ICON);
        $actions[] = new ToolbarItem(Translation :: get('Publish', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_publish.png', $this->get_repository_browser()->get_publish_content_object_url($content_object), ToolbarItem :: DISPLAY_ICON);

        if ($this->get_repository_browser()->get_user()->is_platform_admin())
        {
            $actions[] = new ToolbarItem(Translation :: get('CopyToTemplates'), Theme :: get_common_image_path() . 'export_template.png', $this->get_repository_browser()->get_copy_content_object_url($content_object->get_id(), 0), ToolbarItem :: DISPLAY_ICON);
        }

        if ($content_object instanceof ComplexContentObjectSupport)
        {
            $actions[] = new ToolbarItem(Translation :: get('BuildComplexObject', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_build.png', $this->get_repository_browser()->get_browse_complex_content_object_url($content_object), ToolbarItem :: DISPLAY_ICON);

            $preview_url = $this->get_repository_browser()->get_preview_complex_content_object_url($content_object);
            $onclick = '" onclick="javascript:openPopup(\'' . $preview_url . '\'); return false;';
            $actions[] = new ToolbarItem(Translation :: get('Preview', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_preview.png', $preview_url, ToolbarItem :: DISPLAY_ICON, false, $onclick, '_blank');
        }

        if ($content_object->get_type() == Document :: get_type_name())
        {
            $actions[] = new ToolbarItem(Translation :: get('Download', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_download.png', $this->get_repository_browser()->get_document_downloader_url($content_object->get_id()), ToolbarItem :: DISPLAY_ICON);
        }

        return $actions;
    }

}
?>