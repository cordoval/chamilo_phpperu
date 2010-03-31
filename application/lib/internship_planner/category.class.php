<?php
/**
 * internship_planner
 */
require_once dirname(__FILE__) . '/internship_planner_data_manager.class.php';
/**
 * This class describes a Category data object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipPlannerCategory extends NestedTreeNode
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * Category properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION));
    }

    /**
     * Returns the id of this Category.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Sets the id of this Category.
     * @param id
     */
    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    /**
     * Returns the name of this Category.
     * @return the name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name of this Category.
     * @param name
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the description of this Category.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the description of this Category.
     * @param description
     */
    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    static function get_table_name()
    {
        //		 return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
        return 'category';
    }

    function get_data_manager()
    {
        return InternshipPlannerDataManager :: get_instance();
    }

    function get_locations($include_subcategories = false, $recursive_subcategories = false)
    {
        $dm = $this->get_data_manager();
        
        $categories = array();
        $categories[] = $this->get_id();
        
        if ($include_subcategories)
        {
            $subcategories = $dm->get_internship_planner_category_children($this, $recursive_subcategories);
            
            while ($subcategory = $subcategories->next_result())
            {
                $categories[] = $subcategory->get_id();
            }
        }
        
        $condition = new InCondition(InternshipPlannerCategoryRelLocation :: PROPERTY_CATEGORY_ID, $categories);
        $category_rel_locations = $dm->retrieve_category_rel_locations($condition);
        $locations = array();
        
        while ($category_rel_location = $category_rel_locations->next_result())
        {
            $location_id = $category_rel_location->get_location_id();
            if (! in_array($location_id, $locations))
            {
                $locations[] = $location_id;
            }
        }
        
        return $locations;
    }

    function count_locations($include_subcategories = false, $recursive_subcategories = false)
    {
        $locations = $this->get_locations($include_subcategories, $recursive_subcategories);
        
        return count($locations);
    }

    function truncate()
    {
        return $this->get_data_manager()->truncate_category($this);
    }
}

?>