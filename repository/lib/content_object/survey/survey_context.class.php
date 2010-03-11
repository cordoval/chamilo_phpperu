<?php

require_once (dirname(__FILE__) . '/context_data_manager/context_data_manager.class.php');

abstract class SurveyContext extends DataClass
{
    
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_TYPE = 'type';
    const PROPERTY_NAME = 'name';
    
    private $additionalProperties;

    abstract public function create_contexts_for_user($user_name);

    abstract public function get_display_name();

    public function SurveyContext($defaultProperties = array (), $additionalProperties = null)
    {
        parent :: __construct($defaultProperties);
        $this->additionalProperties = $additionalProperties;
    }

    public function create()
    {
        $dm = SurveyContextDataManager :: get_instance();
        
        if (! $dm->create_survey_context($this))
        {
            return false;
        }
        else
        {
            return true;
        }
    
    }
    
	public function delete(){
	 	
		$dm = SurveyContextDataManager :: get_instance();
        
        if (! $dm->delete_survey_context($this))
        {
            return false;
        }
        else
        {
            return true;
        }
	}
    
    static function factory($type, $defaultProperties = array(), $additionalProperties = array())
    {
        $class = self :: type_to_class($type);
        require_once dirname(__FILE__) . '/context/' . $type . '/' . $type . '.class.php';
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

    function get_type()
    {
        return self :: class_to_type(get_class($this));
    }

    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_TYPE, self :: PROPERTY_NAME));
    }

    private static function get_registered_context_types()
    {
        $path = dirname(__FILE__) . '/context/';
        $folders = Filesystem :: get_directory_content($path, Filesystem :: LIST_DIRECTORIES, false);
        return $folders;
    }

    static function get_registered_contexts()
    {
        $types = SurveyContext :: get_registered_context_types();
        $contexts = array();
        foreach ($types as $type)
        {
            $contexts[] = SurveyContext :: factory($type);
        }
        return $contexts;
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
        $this->additionalProperties = $dm->retrieve_additional_survey_context_properties($this);
    }

    public static function get_by_id($survey_context_id, $type)
    {
        $dm = SurveyContextDataManager :: get_instance();
        return $dm->retrieve_survey_context_by_id($survey_context_id, $type);
    }

}
?>