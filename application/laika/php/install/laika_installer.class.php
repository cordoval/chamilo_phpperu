<?php
/**
 * $Id: laika_installer.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.install
 */
require_once dirname(__FILE__) . '/../laika_data_manager.class.php';

class LaikaInstaller extends Installer
{

    /**
     * Constructor
     */
    function LaikaInstaller($values)
    {
        parent :: __construct($values, LaikaDataManager :: get_instance());
    }

    function install_extra()
    {
        //    	$sql_file = Path :: get_application_path() . 'lib/laika/install/dump.sql';
        //    	$query = file_get_contents($sql_file);
        //    	$queries = explode('@@@@', $query);
        //
        //    	$dm = $this->get_data_manager();
        //    	$connection = $dm->get_connection();
        //
        //    	foreach($queries as $query)
        //    	{
        //			$statement = $connection->query(trim($query));
        //
        //			if (MDB2 :: isError($statement))
        //			{
        //				return false;
        //			}
        //    	}


        return true;
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>