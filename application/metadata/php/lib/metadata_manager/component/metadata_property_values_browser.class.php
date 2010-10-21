<?php 
namespace application\metadata;
/**
 * metadata component which allows the user to browse his metadata_property_values
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataPropertyValuesBrowserComponent extends MetadataManager
{

    function run()
    {
            $this->display_header();

            echo $this->get_table();

            $this->display_footer();
    }

    function get_table()
    {
        $parameters = $this->get_parameters(true);
        $parameters[Application :: PARAM_APPLICATION] = 'metadata';
        $parameters[Application :: PARAM_ACTION] =  MetadataManager::ACTION_BROWSE_METADATA_PROPERTY_VALUES;

        $table = new MetadataPropertyValueBrowserTable($this, $parameters, $condition);

        return $table->as_html();
    }
}
?>