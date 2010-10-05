<?php
require_once dirname(__FILE__).'/../cas_user_data_manager.class.php';

/**
 * @author Hans De Bisschop
 */
class CasUserInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function CasUserInstaller($values)
    {
    	parent :: __construct($values, CasUserDataManager :: get_instance());
    }

	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>