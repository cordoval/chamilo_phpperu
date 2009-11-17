<?php
/**
 * $Id: reservations_installer.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.install
 */
require_once dirname(__FILE__) . '/../reservations_data_manager.class.php';

/**
 * This installer can be used to create the storage structure for the
 * users application.
 */
class ReservationsInstaller extends Installer
{

    /**
     * Constructor
     */
    function ReservationsInstaller($values)
    {
        parent :: __construct($values, ReservationsDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>
