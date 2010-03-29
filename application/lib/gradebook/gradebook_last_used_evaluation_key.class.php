<?php
class GradebookLastUsedEvaluationKey extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'last_used_evaluation_key';
    

    static function get_defualt_property_names()
    {
    	return parent :: get_defualt_property_names(array(self :: PROPERTY_APPLICATION, self :: PROPERTY_PUBLICATION));
    }
    
    function get_data_manager()
    {
    	return GradebookDataManager :: get_instance();
    }
    
    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>