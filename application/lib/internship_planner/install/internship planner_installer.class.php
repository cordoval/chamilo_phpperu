<?php
/**
 * internship planner.install
 */

require_once dirname(__FILE__).'/../internship planner_data_manager.class.php';

/**
 * This installer can be used to create the storage structure for the
 * internship planner application.
 *
 * @author Sven Vanpoucke
 * @author ehb
 */
class Internship plannerInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function Internship plannerInstaller($values)
    {
    	parent :: __construct($values, Internship plannerDataManager :: get_instance());
    }

	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>