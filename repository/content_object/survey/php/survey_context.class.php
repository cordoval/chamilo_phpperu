<?php
namespace repository\content_object\survey;

use common\libraries\Filesystem;
use common\libraries\Utilities;
use common\libraries\DataClass;

require_once (dirname(__FILE__) . '/context_data_manager/context_data_manager.class.php');

abstract class SurveyContext extends DataClass
{

    const CLASS_NAME = __CLASS__;

    const PROPERTY_TYPE = 'type';
    const PROPERTY_NAME = 'name';
    const PROPERTY_CONTEXT_REGISTRATION_ID = 'context_registration_id';
    const PROPERTY_ACTIVE = 'active';

    private $additionalProperties;

    //    abstract static public function create_contexts_for_user($user_id, $key, $key_type = '' );


    abstract static public function get_allowed_keys();

    abstract static function get_additional_property_names();

    //    abstract static public function get_display_name();


    public function SurveyContext($defaultProperties = array (), $additionalProperties = null)
    {
        parent :: __construct($defaultProperties);
        if (isset($additionalProperties))
        {
            $this->additionalProperties = $additionalProperties;
        }

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

    public function delete()
    {

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

    public function update()
    {

        $dm = SurveyContextDataManager :: get_instance();

        if (! $dm->update_survey_context($this))
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
        require_once dirname(__FILE__) . '/context/' . $type . '/' . $type . '.class.php';
        return new $class($defaultProperties, $additionalProperties);
    }

    static function type_to_class($type)
    {
        return __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($type);
    }

    static function class_to_type($class)
    {
        return Utilities :: get_classname_from_namespace($class, true);
    }

    function get_type()
    {
        return self :: class_to_type(get_class($this));
    }

    function set_type()
    {
        $this->set_default_property(self :: PROPERTY_TYPE, self :: class_to_type(get_class($this)));
    }

    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    function get_context_registration_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTEXT_REGISTRATION_ID);
    }

    function set_context_registration_id($context_registration_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTEXT_REGISTRATION_ID, $context_registration_id);
    }

    function set_active($active)
    {
        $this->set_default_property(self :: PROPERTY_ACTIVE, $active);
    }

    function get_active()
    {
        return $this->get_default_property(self :: PROPERTY_ACTIVE);
    }

    function is_active()
    {
        return $this->get_active() == 1;
    }

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(
                self :: PROPERTY_TYPE,
                self :: PROPERTY_NAME,
                self :: PROPERTY_CONTEXT_REGISTRATION_ID,
                self :: PROPERTY_ACTIVE));
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
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
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