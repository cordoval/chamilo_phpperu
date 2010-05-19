<?php
/*
 * @author Ben Vanmassenhove
 */
class ExternalItem extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'external_item';
    /*
     * GradebookEvaluationGrades properties
     */
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_CATEGORY = 'category';
	/**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
    	return parent :: get_default_property_names(array(self :: PROPERTY_TITLE, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_CATEGORY));
    }
    
    function get_data_manager()
    {
    	return GradebookDataManager :: get_instance();
    }
    
    // getters and setters
    
    function get_title()
    {
    	return $this->get_default_property(self :: PROPERTY_TITLE);
    }
    
    function set_title($title)
    {
    	$this->set_default_property(self :: PROPERTY_TITLE, $title);
    }
    
    function get_description()
    {
    	return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }
    
    function set_description($description)
    {
    	$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }
    
    function get_category()
    {
    	return $this->get_default_property(self :: PROPERTY_CATEGORY);
    }
    
    function set_category($category)
    {
    	$this->set_default_property(self :: PROPERTY_CATEGORY, $category);
    }
    
    function get_view_url()
    {
    	
    }
    
    function delete()
    {
    	$dm = $this->get_data_manager();
    	
		$condition = new EqualityCondition(ExternalItemInstance :: PROPERTY_EXTERNAL_ITEM_ID, $this->get_id());
		$instances = $dm->retrieve_external_item_instances($condition);
		
		while ($instance = $instances->next_result())
		{
			$evaluation = $dm->retrieve_evaluation($instance->get_evaluation_id());
			$evaluation->delete();
		}
		
    	parent :: delete();
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>