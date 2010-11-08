<?php
namespace application\metadata;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\OrCondition;
use common\libraries\Request;
use common\libraries\Translation;
use group\GroupDataManager;

/**
 * Component to edit an existing metadata_property_value object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerGroupMetadataEditorComponent extends MetadataManager
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $udm =  GroupDataManager :: get_instance();
        $group = $udm->retrieve_group(Request :: get(MetadataManager :: PARAM_GROUP));

        $condition = new EqualityCondition(GroupMetadataPropertyValue :: PROPERTY_GROUP_ID, $group->get_id());
        $metadata_property_values = $this->retrieve_group_metadata_property_values($condition);
        $metadata_property_values = $this->format_metadata_property_values($metadata_property_values);
 
        $form = new GroupMetadataEditorForm($group, $metadata_property_values, $this->get_url(array(MetadataManager :: PARAM_GROUP => $group->get_id())), $this);

        if($form->validate())
        {
            $success = $form->edit_metadata();
            $this->redirect($success ? Translation :: get('ObjectUpdated', array('OBJECT' => Translation :: get('Metadata')), Utilities :: COMMON_LIBRARY) : Translation :: get('ObjectNotUpdated', array('OBJECT' => Translation :: get('Metadata')), Utilities :: COMMON_LIBRARY), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_EDIT_GROUP_METADATA, MetadataManager :: PARAM_GROUP => $group->get_id()));
        }
        else
        {
            $this->display_header($trail);
            $form->display();
            $this->display_footer();
        }
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
}
?>