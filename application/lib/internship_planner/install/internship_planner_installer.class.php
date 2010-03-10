<?php
/**
 * internship_planner.install
 */

require_once dirname(__FILE__).'/../internship_planner_data_manager.class.php';

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

	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>