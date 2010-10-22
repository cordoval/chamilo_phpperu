<?php
namespace application\metadata;
use common\libraries\Request;
use common\libraries\Translation;

/**
 * Component to edit an existing metadata_default_value object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataDefaultValueUpdaterComponent extends MetadataManager
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $metadata_default_value = $this->retrieve_metadata_default_value(Request :: get(MetadataManager :: PARAM_METADATA_DEFAULT_VALUE));

        $metadata_property_type = $this->retrieve_metadata_property_type($metadata_default_value->get_property_type_id());

        $conditions = array();
        $conditions[] = new EqualityCondition(MetadataAttributeNesting :: PROPERTY_PARENT_ID, $metadata_property_type->get_id());
        $allowed_metadata_property_attribute_types = $this->retrieve_allowed_metadata_property_attribute_types($conditions);

        foreach($allowed_metadata_property_attribute_types[$metadata_property_type->get_id()] as $id)
        {
            $metadata_property_attribute_types[$id] = $this->retrieve_metadata_property_attribute_type($id)->render_name();
        }
        $params[MetadataManager :: PARAM_METADATA_DEFAULT_VALUE] = Request :: get(MetadataManager :: PARAM_METADATA_DEFAULT_VALUE);
        $form = new MetadataDefaultValueForm(MetadataDefaultValueForm :: TYPE_EDIT, $metadata_default_value, $metadata_property_type, $metadata_property_attribute_types, $this->get_url($params));

        if($form->validate())
        {
                $success = $form->update_metadata_default_value();
                $this->redirect($success ? Translation :: get('MetadataPropertyTypeUpdated') : Translation :: get('MetadataPropertyTypeNotUpdated'), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_DEFAULT_VALUES, MetadataManager :: PARAM_METADATA_PROPERTY_TYPE => $metadata_default_value->get_property_type_id()));
        }
        else
        {
                $this->display_header();
                $form->display();
                $this->display_footer();
        }
    }
}
?>