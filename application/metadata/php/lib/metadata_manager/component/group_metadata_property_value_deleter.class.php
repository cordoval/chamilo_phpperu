<?php 
namespace application\metadata;
use common\libraries\Translation;
use common\libraries\Request;
/**
 * Component to delete metadata_property_values objects
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerGroupMetadataPropertyValueDeleterComponent extends MetadataManager
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = $_GET[MetadataManager :: PARAM_METADATA_PROPERTY_VALUE];
        $fail = false;

        if (!empty ($id))
        {
            $metadata_property_value = $this->retrieve_group_metadata_property_value($id);

            if(!$metadata_property_value->delete())
            {
                $message = 'SelectedMetadataPropertyValueNotDeleted';
                $fail = true;
            }
            else
            {
                $message = 'SelectedMetadataPropertyValueDeleted';
            }

            $this->redirect(Translation :: get($message), ($fail ? true : false), array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_EDIT_GROUP_METADATA, MetadataManager :: PARAM_GROUP => Request :: get(MetadataManager :: PARAM_GROUP)));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoMetadataPropertyValuesSelected')));
        }
    }
}
?>