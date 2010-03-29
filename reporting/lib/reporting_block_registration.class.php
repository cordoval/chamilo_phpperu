<?php
/**
 * $Id: reporting_block_registration.class.php
 * Class representing a reporting block
 * @package reporting.lib
 * @author Magali Gillard
 */

class ReportingBlockRegistration extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_APPLICATION = 'application';
    const PROPERTY_BLOCK = 'block';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_APPLICATION, self :: PROPERTY_BLOCK));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return ReportingDataManager :: get_instance();
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

    public function get_block()
    {
        return $this->get_default_property(self :: PROPERTY_BLOCK);
    }
    
    public function get_block_object()
    {
    	return ;
    }

    public function set_block($value)
    {
        $this->set_default_property(self :: PROPERTY_BLOCK, $value);
    }
    
    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
} //class ReportingTemplateRegistration
?>
