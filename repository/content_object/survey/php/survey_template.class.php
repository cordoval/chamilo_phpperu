<?php
namespace repository\content_object\survey;

use common\libraries\DataClass;
use common\libraries\Utilities;

class SurveyTemplate extends DataClass
{

    const CLASS_NAME = __CLASS__;
   
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_CONTEXT_TEMPLATE_ID = 'context_template_id';

//    private $additionalProperties;

//    public function __construct($defaultProperties = array (), $additionalProperties = null)
//    {
//        parent :: __construct($defaultProperties);
//        if (isset($additionalProperties))
//        {
//            $this->additionalProperties = $additionalProperties;
//        }
//
//    }

//    public function create()
//    {
//        $dm = SurveyContextDataManager :: get_instance();
//
//        if (! $dm->create_survey_template_user($this))
//        {
//            return false;
//        }
//        else
//        {
//            return true;
//        }
//
//    }

    public function delete()
    {

        $dm = SurveyContextDataManager :: get_instance();

        if (! $dm->delete_survey_template_user($this))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

//    public function update()
//    {
//
//        $dm = SurveyContextDataManager :: get_instance();
//
//        if (! $dm->update_survey_template_user($this))
//        {
//            return false;
//        }
//        else
//        {
//            return true;
//        }
//    }
   
	
//      /**
//     * Returns the id of this SurveyTemplate.
//     * @return the id.
//     */
//    function get_id()
//    {
//        return $this->get_default_property(self :: PROPERTY_ID);
//    }
//
//    /**
//     * Sets the id of this SurveyTemplate.
//     * @param id
//     */
//    function set_id($id)
//    {
//        $this->set_default_property(self :: PROPERTY_ID, $id);
//    }

    /**
     * Returns the name of this SurveyTemplate.
     * @return the name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name of this SurveyTemplate.
     * @param name
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the description of this SurveyTemplate.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the description of this SurveyTemplate.
     * @param description
     */
    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }
    
   function get_context_template_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTEXT_TEMPLATE_ID);
    }

    function set_context_template_id($context_template_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTEXT_TEMPLATE_ID, $context_template_id);
    }
    
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_CONTEXT_TEMPLATE_ID));
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
   
}
?>