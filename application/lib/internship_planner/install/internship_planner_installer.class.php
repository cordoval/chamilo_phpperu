<?php
/**
 * internship_planner.install
 */

require_once dirname(__FILE__) . '/../internship_planner_data_manager.class.php';
require_once dirname(__FILE__) . '/../category.class.php';

/**
 * This installer can be used to create the storage structure for the
 * internship_planner application.
 *
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipPlannerInstaller extends Installer
{

    /**
     * Constructor
     */
    function InternshipPlannerInstaller($values)
    {
        parent :: __construct($values, InternshipPlannerDataManager :: get_instance());
    }
	
 /**
     * Additional installation steps.
     */
    function install_extra()
    {
        if (! $this->create_root_category())
        {
            return false;
        }
        
        return true;
    }

    function create_root_category()
    {
        $values = $this->get_form_values();
        
        $category = new InternshipPlannerCategory();
        $category->set_name($values['organization_name']);
        $category->set_parent_id(0);
        $succes = $category->create();
        
        return $succes;
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>