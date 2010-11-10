<?php
namespace application\metadata;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\OrCondition;
use common\libraries\Request;
use common\libraries\Translation;
use user\UserDataManager;
use common\libraries\Utilities;

/**
 * Component to edit an existing metadata_property_value object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerUserMetadataEditorComponent extends MetadataManager
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $udm =  UserDataManager :: get_instance();
        $user = $udm->retrieve_user(Request :: get(MetadataManager :: PARAM_USER));

        $condition = new EqualityCondition(UserMetadataPropertyValue :: PROPERTY_USER_ID, $user->get_id());
        $metadata_property_values = $this->retrieve_user_metadata_property_values($condition);
        $metadata_property_values = $this->format_metadata_property_values($metadata_property_values);
 
        $form = new UserMetadataEditorForm($user, $metadata_property_values, $this->get_url(array(MetadataManager :: PARAM_USER => $user->get_id())), $this);

        if($form->validate())
        {
            $success = $form->edit_metadata();
            $this->redirect(Translation :: get($success ? 'ObjectUpdated' : 'ObjectnotUpdated' , array('OBJECT' => Translation :: get('Metadata')), Utilities :: COMMON_LIBRARIES), !$success, array(MetadataManager :: PARAM_ACTION => MetadataManager :: ACTION_EDIT_USER_METADATA, MetadataManager :: PARAM_USER => $user->get_id()));
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