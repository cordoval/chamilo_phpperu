<?php

require_once (dirname(__FILE__) . '/context_data_manager/context_data_manager.class.php');

abstract class SurveyTemplate extends DataClass
{
    
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_TYPE = 'type';
    const PROPERTY_USER_ID = 'user_id';
    
    private $additionalProperties;

    public function SurveyTemplate($defaultProperties = array (), $additionalProperties = null)
    {
        parent :: __construct($defaultProperties);
        if (isset($additionalProperties))
        {
            $this->additionalProperties = $additionalProperties;
        }
    
    }
	
    abstract static function get_additional_property_names($with_context_type =  false);
    
    public function create()
    {
        $dm = SurveyContextDataManager :: get_instance();
        
        if (! $dm->create_survey_template($this))
        {
            return false;
        }
        else
        {
            return true;
        }
    
    }

    public function delete()
    {
        
        $dm = SurveyContextDataManager :: get_instance();
        
        if (! $dm->delete_survey_template($this))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function update()
    {
        
        $dm = SurveyContextDataManager :: get_instance();
        
        if (! $dm->update_survey_template($this))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    static function factory($type, $defaultProperties = array(), $additionalProperties = null)
    {
        $class = self :: type_to_class($type);
        require_once dirname(__FILE__) . '/template/' . $type . '/' . $type . '.class.php';
        return new $class($defaultProperties, $additionalProperties);
    }

    static function type_to_class($type)
    {
        return Utilities :: underscores_to_camelcase($type);
    }

    static function class_to_type($class)
    {
        return Utilities :: camelcase_to_underscores($class);
    }
	
	function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

   
    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }
    
    function get_type()
    {
        return self :: class_to_type(get_class($this));
    }
	
    
    
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_TYPE, self :: PROPERTY_USER_ID));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return SurveyContextDataManager :: get_instance();
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    function get_additional_property($name)
    {
        $this->check_for_additional_properties();
        return $this->additionalProperties[$name];
    }

    function set_additional_property($name, $value)
    {
        //$this->check_for_additional_properties();
        $this->additionalProperties[$name] = $value;
    }

    function get_additional_properties()
    {
        $this->check_for_additional_properties();
        return $this->additionalProperties;
    }

    private function check_for_additional_properties()
    {
        if (isset($this->additionalProperties))
        {
            return;
        }
        $dm = SurveyContextDataManager :: get_instance();
        $this->additionalProperties = $dm->retrieve_additional_survey_template_properties($this);
    }

    public static function get_by_id($survey_template_id, $type)
    {
        $dm = SurveyContextDataManager :: get_instance();
        return $dm->retrieve_survey_template_by_id($survey_template_id, $type);
    }

}
?>