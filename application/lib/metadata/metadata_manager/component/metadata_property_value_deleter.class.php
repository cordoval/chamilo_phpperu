<?php
/**
 * @package application.metadata.metadata.component
 */
require_once dirname(__FILE__).'/../metadata_manager.class.php';

/**
 * Component to delete metadata_property_values objects
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataPropertyValueDeleterComponent extends MetadataManager
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = $_GET[MetadataManager :: PARAM_METADATA_PROPERTY_VALUE];
        $fail = false;

        if (!empty ($id))
        {
            $metadata_property_value = $this->retrieve_metadata_property_value($id);

            if(!$metadata_property_value->delete())
            {
                $message = 'SelectedMetadataPropertyValueNotDeleted';
                $fail = true;
            }
            else
            {
                $message = 'SelectedMetadataPropertyValueDeleted';
            }

            $this->redirect(Translation :: get($message), ($fail ? true : false), array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_EDIT_METADATA, MetadataManager :: PARAM_CONTENT_OBJECT => Request :: get(MetadataManager :: PARAM_CONTENT_OBJECT)));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoMetadataPropertyValuesSelected')));
        }
    }
}
?>