<?php
/**
 * $Id: survey_installer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.install
 */

require_once dirname(__FILE__) . '/../lib/survey_data_manager.class.php';

/**
 * This installer can be used to create the storage structure for the
 * survey application.
 *
 * @author Sven Vanpoucke
 * @author 
 */
class SurveyInstaller extends Installer
{

    /**
     * Constructor
     */
    function SurveyInstaller($values)
    {
        parent :: __construct($values, SurveyDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>