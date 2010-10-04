<?php
/**
 * @package application.metadata.metadata.component
 */
require_once dirname(__FILE__).'/../metadata_manager.class.php';
require_once dirname(__FILE__).'/../../forms/metadata_form.class.php';

/**
 * Component to edit an existing metadata_property_value object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataEditorComponent extends MetadataManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
            $rdm =  RepositoryDataManager :: get_instance();
            $content_object = $rdm->retrieve_content_object(Request :: get(MetadataManager :: PARAM_CONTENT_OBJECT));

            $content_object_property_metadata = $this->get_content_object_property_metadata_values($content_object);

            $condition = new EqualityCondition(MetadataPropertyValue :: PROPERTY_CONTENT_OBJECT_ID, $content_object->get_id());
            $metadata_property_values = $this->retrieve_metadata_property_values($condition);
            $metadata_property_values = $this->format_metadata_property_values($metadata_property_values);

            $content_object_condition1 =  new EqualityCondition(MetadataPropertyAttributeValue :: PROPERTY_PARENT_ID, Request :: get(MetadataManager :: PARAM_CONTENT_OBJECT));
            $content_object_condition2 = new EqualityCondition(MetadataPropertyAttributeValue :: PROPERTY_RELATION, MetadataPropertyAttributeValue :: RELATION_CONTENT_OBJECT_PROPERTY);
            $content_object_condition = new AndCondition($content_object_condition1, $content_object_condition2);
            
            $property_value_conditions = array();

            foreach($metadata_property_values as $id => $metadata_property_value)
            {
                $property_value_conditions[] = new EqualityCondition(MetadataPropertyAttributeValue :: PROPERTY_PARENT_ID, $metadata_property_value->get_id());
                $conditions_allowed[] = new EqualityCondition(MetadataAttributeNesting :: PROPERTY_PARENT_ID, $metadata_property_value->get_property_type_id());
            }

            $metadata_property_attribute_values = array();

            if(count($property_value_conditions))
            {
                $property_value_condition1 = new OrCondition($property_value_conditions);
                $property_value_condition2 = new EqualityCondition(MetadataPropertyAttributeValue :: PROPERTY_RELATION, MetadataPropertyAttributeValue :: RELATION_PROPERTY_VALUE);
                $property_value_condition = new AndCondition($property_value_condition1, $property_value_condition2);

                $condition = new OrCondition($content_object_condition, $property_value_condition);

                $metadata_property_attribute_values = $this->retrieve_metadata_property_attribute_values($condition);
                $metadata_property_attribute_values = $this->format_metadata_property_attribute_values($metadata_property_attribute_values);
            }

            $allowed_metadata_property_attribute_types = array();

            foreach($content_object_property_metadata as $id => $value)
            {
                if(!isset($metadata_property_values[$id]))
                {
                    $conditions_allowed[] = new EqualityCondition(MetadataAttributeNesting :: PROPERTY_PARENT_ID, $id);
                }
            }

            if(count($conditions_allowed))
            {
                $or_condition = new OrCondition($conditions_allowed);
                $type_condition = new EqualityCondition(MetadataAttributeNesting :: PROPERTY_CHILD_TYPE, MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_TYPE);
                $condition = new AndCondition($or_condition, $type_condition);

                $allowed_metadata_property_attribute_types = $this->retrieve_metadata_attribute_nestings($condition);
                $allowed_metadata_property_attribute_types = $this->format_allowed_metadata_property_attribute_types($allowed_metadata_property_attribute_types);
            }

            $form = new MetadataForm(MetadataForm :: TYPE_EDIT, $content_object, $metadata_property_values, $content_object_property_metadata, $metadata_property_attribute_values, $allowed_metadata_property_attribute_types,$this->get_url(array(MetadataManager :: PARAM_CONTENT_OBJECT => $content_object->get_id())), $this->get_user(), $this);

            if($form->validate())
            {
                    $success = $form->edit_metadata_property_values();
                    $this->redirect($success ? Translation :: get('MetadataPropertyValueUpdated') : Translation :: get('MetadataPropertyValueNotUpdated'), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_EDIT_METADATA_PROPERTY_VALUES, MetadataManager :: PARAM_CONTENT_OBJECT => $content_object->get_id()));
            }
            else
            {
                    $this->display_header($trail);
                    $form->display();
                    $this->display_footer();
            }
	}

        function get_content_object_property_metadata_values($content_object)
        {
            $content_object_property_metadatas = $this->retrieve_content_object_property_metadatas();
            $content_object_property_metadata_values = array();

            while($content_object_property_metadata = $content_object_property_metadatas->next_result())
            {
                $content_object_property_metadata_values[$content_object_property_metadata->get_id()] = $content_object_property_metadata;
            }
            return $content_object_property_metadata_values;
        }

        function format_metadata_property_values($metadata_property_values)
        {
            $metadata_property_value_arr = array();

            while($metadata_porperty_value = $metadata_property_values->next_result())
            {
                $metadata_property_value_arr[$metadata_porperty_value->get_id()] = $metadata_porperty_value;
            }
            return $metadata_property_value_arr;
        }

        function format_metadata_property_attribute_values($metadata_property_attribute_values)
        {
            $metadata_property_attribute_value_arr = array();

            while($metadata_property_attribute_value = $metadata_property_attribute_values->next_result())
            {
                $metadata_property_attribute_value_arr[$metadata_property_attribute_value->get_relation()][$metadata_property_attribute_value->get_parent_id()][$metadata_property_attribute_value->get_id()] = $metadata_property_attribute_value;
            }
            return $metadata_property_attribute_value_arr;
        }

        function format_allowed_metadata_property_attribute_types($allowed_metadata_property_attribute_types)
        {
            $allowed_metadata_property_attribute_type_arr = array();

            while($allowed_metadata_property_attribute_type = $allowed_metadata_property_attribute_types->next_result())
            {
                $allowed_metadata_property_attribute_type_arr[$allowed_metadata_property_attribute_type->get_parent_id()][$allowed_metadata_property_attribute_type->get_child_id()] = $allowed_metadata_property_attribute_type->get_child_id();
            }
            return $allowed_metadata_property_attribute_type_arr;
        }
}
?>