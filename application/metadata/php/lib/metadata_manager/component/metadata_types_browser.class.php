<?php
namespace application\metadata;

use common\libraries\Application;
use common\libraries\Translation;

/**
 * metadata component which allows the user to browse his metadata_property_values
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataTypesBrowserComponent extends MetadataManager
{

    function run()
    {
        $this->display_header();

        $param_user = array(
                MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_USER_METADATA_PROPERTY_VALUES);
        $param_group = array(
                MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_GROUP_METADATA_PROPERTY_VALUES);
        $param_content_object = array(
                MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_CONTENT_OBJECT_METADATA_PROPERTY_VALUES);

        echo '<a href="' . $this->get_url($param_user) . '">' . Translation :: get('UserMetadata') . '</a><br />' . "\n";
        echo '<a href="' . $this->get_url($param_group) . '">' . Translation :: get('GroupMetadata') . '</a><br />' . "\n";
        echo '<a href="' . $this->get_url($param_content_object) . '">' . Translation :: get('ContentObjectMetadata') . '</a><br />' . "\n";

        $this->display_footer();
    }

    function get_table()
    {
        $parameters = $this->get_parameters(true);
        $parameters[Application :: PARAM_APPLICATION] = 'metadata';
        $parameters[Application :: PARAM_ACTION] = MetadataManager :: ACTION_BROWSE_METADATA_CONTENT_OBJECT_PROPERTY_VALUES;

        $table = new ContentObjectMetadataPropertyValueBrowserTable($this, $parameters, $condition);

        return $table->as_html();
    }
}

?>
