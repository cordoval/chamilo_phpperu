<?php
namespace application\metadata;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Application;
use common\libraries\ActionBarRenderer;
use common\libraries\Theme;
use common\libraries\Utilities;

/**
 * metadata component which allows the user to browse his content_object_property_metadatas
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerContentObjectPropertyMetadatasBrowserComponent extends MetadataManager
{
    function run()
    {
        
        $this->display_header();

        $html = array();

        $html[] = $this->get_action_bar()->as_html();
        $html[] = $this->get_table();
        
        echo implode("\n", $html);

        $this->display_footer();
    }

    function get_table()
    {
        $parameters = $this->get_parameters(true);
        $parameters[Application :: PARAM_APPLICATION] = 'metadata';
        $parameters[Application :: PARAM_ACTION] =  MetadataManager::ACTION_BROWSE_CONTENT_OBJECT_PROPERTY_METADATAS;

        $table = new ContentObjectPropertyMetadataBrowserTable($this, $parameters, $condition);

        return $table->as_html();
    }

    function get_action_bar()
    {
       $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $actions = array();
        $actions[] = new ToolbarItem(Translation :: get('CreateObject', array('OBJECT' => Translation :: get('ContentObjectPropertyMetadata')), Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_CREATE_CONTENT_OBJECT_PROPERTY_METADATA)));

        $action_bar->set_common_actions($actions);
        $action_bar->set_search_url($this->get_url());

        return $action_bar;
    }
}
?>