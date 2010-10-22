<?php
namespace application\metadata;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\EqualityCondition;

/**
 * Component to create a new metadata_default_value object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataDefaultValueCreatorComponent extends MetadataManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
            $metadata_property_type_id = Request :: get(MetadataManager :: PARAM_METADATA_PROPERTY_TYPE);
            if(!isset($metadata_property_type_id))
            {
                exit(Translation :: get('NoMetadataPropertyType'));
            }

            $metadata_property_type = $this->retrieve_metadata_property_type($metadata_property_type_id);

            $conditions = array();
            $conditions[] = new EqualityCondition(MetadataAttributeNesting :: PROPERTY_PARENT_ID, $metadata_property_type->get_id());
            $allowed_metadata_property_attribute_types = $this->retrieve_allowed_metadata_property_attribute_types($conditions);

            foreach($allowed_metadata_property_attribute_types[$metadata_property_type->get_id()] as $id)
            {
                $metadata_property_attribute_types[$id] = $this->retrieve_metadata_property_attribute_type($id)->render_name();
            }

            $metadata_default_value = new MetadataDefaultValue();


            $form = new MetadataDefaultValueForm(MetadataDefaultValueForm :: TYPE_CREATE, $metadata_default_value, $metadata_property_type, $metadata_property_attribute_types, $this->get_url(array( MetadataManager :: PARAM_METADATA_PROPERTY_TYPE => Request :: get(MetadataManager :: PARAM_METADATA_PROPERTY_TYPE))));

            if($form->validate())
            {
                    $success = $form->create_metadata_default_value();
                    $this->redirect($success ? Translation :: get('MetadataDefaultValueCreated') : Translation :: get('MetadataDefaultValueNotCreated'), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_BROWSE_METADATA_DEFAULT_VALUES, MetadataManager :: PARAM_METADATA_DEFAULT_VALUE => $success->get_id(), MetadataManager :: PARAM_METADATA_PROPERTY_TYPE => Request :: get(MetadataManager :: PARAM_METADATA_PROPERTY_TYPE)));
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