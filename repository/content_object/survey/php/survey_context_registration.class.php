<?php 
namespace repository\content_object\survey;

use \common\libraries\Path;
use \common\libraries\DataClass;


require_once (dirname(__FILE__) . '/context_data_manager/context_data_manager.class.php');
require_once Path :: get_repository_content_object_path() . 'survey/php/survey_context_manager_rights.class.php';


class SurveyContextRegistration extends DataClass
{
    
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_TYPE = 'type';
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_OWNER_ID = 'owner_id';

    public function create()
    {
        $succes = parent :: create();
        if ($succes)
        {
            $parent_location = SurveyContextManagerRights :: get_survey_context_manager_subtree_root_id();
            $location = SurveyContextManagerRights :: create_location_in_survey_context_manager_subtree($this->get_name(), $this->get_id(), $parent_location, SurveyContextManagerRights :: TYPE_CONTEXT_REGISTRATION, true);
            
            $rights = SurveyContextManagerRights :: get_available_rights_for_context_registrations();
            foreach ($rights as $right)
            {
                RightsUtilities :: set_user_right_location_value($right, $this->get_owner_id(), $location->get_id(), 1);
            }
        }
        return $succes;
    }

    public function delete()
    {
        $location = SurveyContextManagerRights :: get_location_by_identifier_from_survey_context_manager_subtree($this->get_id(), SurveyContextManagerRights :: TYPE_CONTEXT_REGISTRATION);
        if ($location)
        {
            if (! $location->remove())
            {
                return false;
            }
        }
        $succes = parent :: delete();
        return $succes;
    }

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_TYPE, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_OWNER_ID));
    }

    function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }

    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    function get_owner_id()
    {
        return $this->get_default_property(self :: PROPERTY_OWNER_ID);
    }

    function set_owner_id($owner_id)
    {
        $this->set_default_property(self :: PROPERTY_OWNER_ID, $owner_id);
    }

    function get_data_manager()
    {
        return SurveyContextDataManager :: get_instance();
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
    }

    function update()
    {
        $this->get_data_manager()->update_survey_context_registration($this);
    }
}
?>