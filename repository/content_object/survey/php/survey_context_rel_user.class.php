<?php 
namespace repository\content_object\survey;

use \common\libraries\DataClass;

class SurveyContextRelUser extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * SurveyContextRelUser properties
     */
    const PROPERTY_CONTEXT_ID = 'context_id';
    const PROPERTY_USER_ID = 'user_id';
    
    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_CONTEXT_ID, self :: PROPERTY_USER_ID);
    }

    function get_data_manager()
    {
        return SurveyContextDataManager :: get_instance();
    }

    /**
     * Returns the context_id of this SurveyContextRelUser.
     * @return the context_id.
     */
    function get_context_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTEXT_ID);
    }

    /**
     * Sets the context_id of this SurveyContextRelUser.
     * @param context_id
     */
    function set_context_id($context_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTEXT_ID, $context_id);
    }

    /**
     * Returns the user_id of this SurveyContextRelUser.
     * @return the user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Sets the user_id of this SurveyContextRelUser.
     * @param user_id
     */
    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }
   

    static function get_table_name()
    {
   		return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
    }
}

?>