<?php
/**
 * internship_organizer.install
 */

require_once dirname(__FILE__) . '/../internship_organizer_data_manager.class.php';
require_once dirname(__FILE__) . '/../category.class.php';

class InternshipOrganizerInstaller extends Installer
{

    /**
     * Constructor
     */
    function InternshipOrganizerInstaller($values)
    {
        parent :: __construct($values, InternshipOrganizerDataManager :: get_instance());
    }
	
 /**
     * Additional installation steps.
     */
    function install_extra()
    {
    	$success = false;
        if ($this->create_root_category())
        {
            $success = true;
        }
    	if (! $this->create_root_category() && $success)
        {
            $success = true;
        }
        return $success;
    }

    function create_root_category()
    {
        $values = $this->get_form_values();
        
        $category = new InternshipOrganizerCategory();
        $category->set_name($values['organization_name']);
        $category->set_parent_id(0);
        $succes = $category->create();
        
        return $succes;
    }
	function create_root_region()
    {
        $values = $this->get_form_values();
        
        $category = new InternshipOrganizerRegion();
        $category->set_name($values['world']);
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