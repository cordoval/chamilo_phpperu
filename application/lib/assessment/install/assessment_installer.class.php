<?php
/**
 * $Id: assessment_installer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.install
 */

require_once dirname(__FILE__) . '/../assessment_data_manager.class.php';

/**
 * This installer can be used to create the storage structure for the
 * assessment application.
 *
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentInstaller extends Installer
{

    /**
     * Constructor
     */
    function AssessmentInstaller($values)
    {
        parent :: __construct($values, AssessmentDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>