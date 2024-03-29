<?php
namespace application\internship_organizer;

use common\libraries\WebApplication;
use common\libraries\Installer;
use common\libraries\Translation;
/**
 * internship_organizer.install
 */

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'internship_organizer_data_manager.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'category.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'region.class.php';

class InternshipOrganizerInstaller extends Installer
{

    /**
     * Constructor
     */
    function __construct($values)
    {
        parent :: __construct($values, InternshipOrganizerDataManager :: get_instance());
    }

    /**
     * Additional installation steps.
     */
    function install_extra()
    {
        $success = true;
        if (! $this->create_root_category())
        {
            $success &= false;
        }
        if (! $this->create_root_region())
        {
            $success &= false;
        }
        if (! $this->create_root_period())
        {
            $success &= false;
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
        
        $region = new InternshipOrganizerRegion();
        $region->set_city_name(Translation :: get('World'));
        $region->set_parent_id(0);
        $succes = $region->create();
        
        return $succes;
    }

    function create_root_period()
    {
        $values = $this->get_form_values();
        
        $period = new InternshipOrganizerPeriod();
        $period->set_name($values['organization_name']);
        $period->set_parent_id(0);
        $succes = $period->create();
        
        return $succes;
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>