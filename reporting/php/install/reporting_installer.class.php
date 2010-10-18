<?php
/**
 * $Id: reporting_installer.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.ajax
 */

class ReportingInstaller extends Installer
{

    function ReportingInstaller($values)
    {
        parent :: __construct($values, ReportingDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>