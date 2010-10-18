<?php

/**
 * @package application.metadata.metadata.component
 */

require_once dirname(__FILE__) . '/../metadata_manager.class.php';
require_once dirname(__FILE__) . '/metadata_property_attribute_type_browser/metadata_property_attribute_type_browser_table.class.php';
/**
 * metadata component which allows the user to browse his metadata_property_attribute_types
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataPropertyAttributeTypesBrowserComponent extends MetadataManager
{

    function run()
    {
        $this->display_header($trail);

        $html = array();

        $action_bar = $this->get_action_bar();
        $html[] = $action_bar->as_html();

        $html[] = $this->get_table();

        echo implode("\n", $html);

        $this->display_footer();
    }

    function get_table()
    {
        $parameters = $this->get_parameters(true);
        $parameters[Application :: PARAM_APPLICATION] = 'metadata';
        $parameters[Application :: PARAM_ACTION] =  MetadataManager::ACTION_BROWSE_METADATA_PROPERTY_ATTRIBUTE_TYPES;

        $table = new MetadataPropertyAttributeTypeBrowserTable($this, $parameters, $condition);

        return $table->as_html();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $actions = array();
        $actions[] = new ToolbarItem(Translation :: get('CreateMetadataPropertyAttributeType'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_CREATE_METADATA_PROPERTY_ATTRIBUTE_TYPE)));

        $action_bar->set_common_actions($actions);
        $action_bar->set_search_url($this->get_url());

        return $action_bar;
    }

}
?>