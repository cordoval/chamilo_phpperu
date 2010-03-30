<?php
/**
 * $Id: reporting_template_registration.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * Class representing a reporting template
 * @package reporting.lib
 * @author Michael Kyndt
 */


class ReportingTemplateRegistration extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_APPLICATION = 'application';
    const PROPERTY_TEMPLATE = 'template';
    const PROPERTY_PLATFORM = 'platform';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
    	return parent :: get_default_property_names(array(self :: PROPERTY_APPLICATION, self :: PROPERTY_TEMPLATE, self :: PROPERTY_PLATFORM));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return ReportingDataManager :: get_instance();
    }
    
    /**
     * Checks if the reporting template registration is aplatform template.
     * @return int
     */
    function isPlatformTemplate()
    {
        return $this->get_default_property(self :: PROPERTY_PLATFORM) == '1';
    }

    /*
 	 * Getters and setters
 	 */
    public function get_application()
    {
        return $this->get_default_property(self :: PROPERTY_APPLICATION);
    }

    public function set_application($value)
    {
        $this->set_default_property(self :: PROPERTY_APPLICATION, $value);
    }

    public function get_template()
    {
    	return $this->get_default_property(self :: PROPERTY_TEMPLATE);
    }

    public function set_template($value)
    {
        $this->set_default_property(self :: PROPERTY_TEMPLATE, $value);
    }

    public function get_platform()
    {
        return $this->get_default_property(self :: PROPERTY_PLATFORM);
    }

    public function set_platform($value)
    {
        $this->set_default_property(self :: PROPERTY_PLATFORM, $value);
    }

    static function get_table_name()
    {   	
    	return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
} //class ReportingTemplateRegistration
?>
