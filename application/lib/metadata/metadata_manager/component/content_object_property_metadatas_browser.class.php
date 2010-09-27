<?php

/**
 * @package application.metadata.metadata.component
 */

require_once dirname(__FILE__) . '/../metadata_manager.class.php';
require_once dirname(__FILE__) . '/content_object_property_metadata_browser/content_object_property_metadata_browser_table.class.php';

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
        $html[] = '<a href="' . $this->get_create_content_object_property_metadata_url() . '">' . Translation :: get('CreateContentObjectPropertyMetadata') . '</a>';
        $html[] = '<br /><br />';

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
        $action_bar->set_search_url($this->get_url());

        return $action_bar;
    }
}
?>