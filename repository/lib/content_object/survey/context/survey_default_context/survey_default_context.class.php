<?php
require_once (dirname(__FILE__) . '/../../survey_context.class.php');

class SurveyDefaultContext extends SurveyContext
{
    
    const CLASS_NAME = __CLASS__;
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_DEFAULT_KEY = 'NOCONTEXT';
    
    private $default_context;

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_DESCRIPTION);
    }

    function get_description()
    {
        return $this->get_additional_property(self :: PROPERTY_DESCRIPTION);
    }

    function set_description($description)
    {
        $this->set_additional_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    static public function get_display_name()
    {
        return Translation :: get('DefaultContext');
    }

    /**
     * @param unknown_type $user_name
     */
    static public function create_contexts_for_user($key, $key_type = self :: PROPERTY_DEFAULT_KEY)
    {
        
        if ($key_type == self :: PROPERTY_DEFAULT_KEY)
        {
            
            $condition = new EqualityCondition(SurveyContext :: PROPERTY_TYPE, SurveyContext :: class_to_type(self :: CLASS_NAME), SurveyContext :: get_table_name() );
            $dm = SurveyContextDataManager :: get_instance();
            $contexts = $dm->retrieve_survey_contexts(self :: get_table_name(), $condition);
            $context = $contexts->next_result();
            if (isset($context))
            {
                return array($context);
            }
            else
            {
                $context = new SurveyDefaultContext();
                $context->set_name(self :: PROPERTY_DEFAULT_KEY);
                $context->set_description('Default Dummy Context');
                $context->create();
                return array($context);
            }
        }
        else
        {
            return array();
        }
    
    }

    static public function get_allowed_keys()
    {
        return array(self :: PROPERTY_DEFAULT_KEY);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

}

?>